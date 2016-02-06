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

        $regionRepo = $eveRegistry->get('EveBundle:Region');
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
            $errors = [];
            list($processableData, $index) = $this->buildIndex($items);
            $pool = new Pool($client, $requests($region, $items), [
                'concurrency' => 10,
                'fulfilled' => function($response, $index) use (&$processableData, $progress, $log) {
                    $obj = json_decode($response->getBody()->getContents(), true);
                    $processableData[$index] = array_pop($obj['items']);
                    $progress->advance();
                },
                'rejected' => function($reason, $index) use ($log, $progress) {
                    $errors[$index] = $reason;
                    $progress->advance();
                }
            ]);

            $promise = $pool->promise();
            $promise->wait();

            $real_region = $regionRepo->getRegionById($region);

            $count = 0;
            foreach ($processableData as $i => $processableItem){
                if (is_array($processableItem) && isset($index[$i])) {
                    $exists = $em->getRepository('EveBundle:ItemPrice')
                        ->hasItem($real_region['regionID'], $index[$i]['typeID']);

                    var_dump($exists);die;
                    $p = $this->makePriceData($processableItem, $real_region, $index[$i]);
                    $em->persist($p);
                    $count++;
                    $log->addDebug("Adding item {$p->getTypeName()} in {$p->getRegionName()}");

                    if ($count % (count($processableData) / 20) === 0){
                        $em->flush();
                        $em->clear();
                    }
                }
            }

        }

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
