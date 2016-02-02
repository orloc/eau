<?php

namespace EveBundle\Command;

use AppBundle\Entity\BuybackConfiguration;
use EveBundle\Entity\ItemPrice;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRegionPricesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evedata:load_region_data')
            ->setDescription('Loads region specific price history from the crest API.');
    }

    protected function buildIndex($items){
        $index = [];
        $data = [];
        foreach ($items as $i){
            $index[] = $i;
            $data[] = null;
        }

        return [ $data, $index ];
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $registry = $this->getContainer()->get('doctrine');
        $log = $this->getContainer()->get('logger');
        $em = $registry->getManager('eve_data');

        $eveRegistry = $this->getContainer()->get('evedata.registry');


        $items = $eveRegistry->get('EveBundle:ItemType')
            ->findAllMarketItems();

        // regions we actually need
        $configs = $registry->getManager()->getRepository('AppBundle:BuybackConfiguration')
            ->findBy(['type' => BuybackConfiguration::TYPE_REGION]);

        $neededRegions = array_reduce($configs, function($carry, $value){
            if ($carry === null){
                return $value->getRegions();
            }
            return array_merge($carry, $value->getRegions());
        });

        $progress = new ProgressBar($output, count($neededRegions) * count($items));
        $progress->setFormat('<comment> %current%/%max% </comment>[%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <question>%memory:6s%</question> <info> %message% </info>');

        $log->addDebug("Beginning Import");



        $client = new Client();
        $requests = function($region, $items) {
            foreach ($items as $i){
                yield new Request('GET', $this->getCrestUrl($region, $i['typeID']));
            }
        };

        foreach ($neededRegions as $region){
            list($processableData, $index) = $this->buildIndex($items);
            $pool = new Pool($client, $requests($region, $items), [
                'concurrency' => 10,
                'fulfilled' => function($response, $index) use ($processableData, $progress, $log) {
                    $obj = json_decode($response->getBody()->getContents(), true);
                    $processableData[$index] = array_pop($obj['items']);
                    $progress->advance();
                },
                'rejected' => function($reason, $index) use ($log, $progress) {
                    $log->addError(sprintf("Failed request for : %s with %s", 'thing', 'thing'));
                    $progress->advance();
                }
            ]);

            $promise = $pool->promise();
            $promise->wait();

            foreach ($processableData as $i => $processableItem){
                if (is_array($processableItem) && isset($index[$i])) {
                    $p = $this->makePriceData($processableItem, $region, $index[$i]['typeID']);
                    $em->persist($p);
                    $log->addDebug("Adding item {$p->getTypeName()} in {$p->getRegionName()}");
                }
            }
        }

        /*
        foreach ($neededRegions as $r){
            $r = $eveRegistry->get('EveBundle:Region')->getRegionById($r);
            $progress->setMessage("Processing Region {$r['regionName']}");
            foreach ($items as $k => $i){
                $url = $this->getCrestUrl($r['regionID'], $i['typeID']);

                try {
                    $response = $client->get($url);
                    $obj = json_decode($response->getBody()->getContents(), true);

                    $processableItem = array_pop($obj['items']);
                    if (is_array($processableItem)) {
                        $p = $this->makePriceData($processableItem, $r, $i);
                        $em->persist($p);
                        $log->addDebug("Adding item {$p->getTypeName()} in {$p->getRegionName()}");
                    }

                }  catch (\Exception $e){
                    $log->addError(sprintf("Failed request for : %s with %s", $url, $e->getMessage()));
                }

                $progress->advance();
            }
        }
        $em->clear();

        */
        $em->flush();
        $progress->finish();
    }

    protected function makePriceData(array $data, array $region, array $item){
        $price = new ItemPrice();

        $price->setTypeId($item['typeID'])
            ->setTypeName($item['typeName'])
            ->setRegionId($region['regionID'])
            ->setRegionName($region['regionName'])
            ->setVolume($data['volume'])
            ->setOrderCount($data['orderCount'])
            ->setHighPrice($data['highPrice'])
            ->setLowPrice($data['lowPrice'])
            ->setAvgPrice($data['avgPrice'])
            ->setDate(new \DateTime($data['date']));

        return $price;
    }

    protected function getCrestUrl($region, $item){
        $baseUri = 'https://public-crest.eveonline.com/';
        return $baseUri."market/$region/types/$item/history/";
    }

}
