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

    protected function buildIndex($items)
    {
        $index = [];
        $data = [];
        foreach ($items as $i) {
            $index[] = $i;
            $data[] = null;
        }

        return [$data, $index];
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

        $chunked_items = array_chunk($items, count($items) / 30);

        // regions we actually need
        $configs = $registry->getManager()->getRepository('AppBundle:BuybackConfiguration')
            ->findBy(['type' => BuybackConfiguration::TYPE_REGION]);

        $neededRegions = array_reduce($configs, function ($carry, $value) {
            if ($carry === null) {
                return $value->getRegions();
            }

            return array_merge($carry, $value->getRegions());
        });

        $log->addDebug('Beginning Import');

        $client = new Client();
        $requests = function ($region, $items) {
            foreach ($items as $i) {
                yield new Request('GET', $this->getCrestUrl($region, $i['typeID']));
            }
        };

        foreach ($neededRegions as $region) {
            $errors = [];
            $real_region = $regionRepo->getRegionById($region);
            foreach ($chunked_items as $items) {
                $progress = new ProgressBar($output, count($items));
                $progress->setFormat('<comment> %current%/%max% </comment>[%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <question>%memory:6s%</question> <info> Updating %message% </info>');

                list($processableData, $index) = $this->buildIndex($items);

                $progress->setMessage($real_region['regionName']);

                $pool = new Pool($client, $requests($region, $items), [
                    'fulfilled' => function ($response, $i) use (&$processableData, $progress, $log) {
                        $obj = json_decode($response->getBody()->getContents(), true);
                        $processableData[$i] = array_pop($obj['items']);
                        $progress->advance();
                    },
                    'rejected' => function ($reason, $i) use ($log, $progress, $real_region) {
                        $errors[$i] = $reason;
                        $progress->setMessage(sprintf("{$real_region['regionName']} with %s errors", count($errors)));
                        $progress->advance();
                    },
                ]);

                $promise = $pool->promise();
                $promise->wait();

                $progress->finish();
                $progress = new ProgressBar($output, count($processableData));
                $progress->setFormat('<comment> %current%/%max% </comment>[%bar%] %percent:3s%%  <question>%memory:6s%</question> <info> Updating Database </info>');

                $count = 0;
                foreach ($processableData as $i => $processableItem) {
                    if (is_array($processableItem) && isset($index[$i])) {
                        $exists = $em->getRepository('EveBundle:ItemPrice')
                            ->hasItem($real_region['regionID'], $index[$i]['typeID']);

                        if ($exists instanceof ItemPrice) {
                            $p = $this->updatePriceData($processableItem, $exists);
                            $log->addDebug("Updating item {$p->getTypeName()} in {$p->getRegionName()}");
                        } else {
                            $p = $this->makePriceData($processableItem, $real_region, $index[$i]);
                            $log->addDebug("Adding item {$p->getTypeName()} in {$p->getRegionName()}");
                        }
                        $progress->advance();

                        $em->persist($p);
                        ++$count;

                        if ($count % (count($processableData) / 20) === 0) {
                            $log->addDebug('Flushing Set');
                            $em->flush();
                            $em->clear();
                        }
                    }
                }
                $progress->finish();
                $em->flush();
            }
        }
    }

    protected function updatePriceData(array $item, ItemPrice $price)
    {
        $price->setVolume($item['volume'])
            ->setOrderCount($item['orderCount'])
            ->setHighPrice($item['highPrice'])
            ->setLowPrice($item['lowPrice'])
            ->setAvgPrice($item['avgPrice'])
            ->setDate(new \DateTime($item['date']));

        return $price;
    }

    protected function makePriceData(array $data, array $region, array $item)
    {
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

    protected function getCrestUrl($region, $item)
    {
        $baseUri = 'https://public-crest.eveonline.com/';

        return $baseUri."market/$region/types/$item/history/";
    }
}
