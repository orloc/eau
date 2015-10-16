<?php

namespace EveBundle\Command;

use EveBundle\Entity\ItemPrice;
use GuzzleHttp\Client;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $registry = $this->getContainer()->get('doctrine');
        $em = $registry->getManager('eve_data');

        $eveRegistry = $this->getContainer()->get('evedata.registry');

        $client = new Client([
            'base_uri' => 'https://public-crest.eveonline.com/'
        ]);

        $regions = $eveRegistry->get('EveBundle:Region')->getAll();
        $items = $eveRegistry->get('EveBundle:ItemType')
            ->findAllMarketItems();

        $progress = new ProgressBar($output, count($regions) * count($items));
        $progress->setFormat('<comment> %current%/%max% </comment>[%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <question>%memory:6s%</question> <info> %message% </info>');

        $itemPriceRepo = $em->getRepository('EveBundle:ItemPrice');

        foreach ($regions as $r){
            $progress->setMessage("Processing Region {$r['regionName']}");
            foreach ($items as $i){
                $url = $this->getCrestUrl($r['regionID'], $i['typeID']);
                $response = $client->get($url);

                $obj = json_decode($response->getBody()->getContents(), true);

                $processableItems = array_slice(array_reverse($obj['items']), 0, 1);


                foreach ($processableItems as $idx => $item){
                    /*
                    $exists = $itemPriceRepo->hasItem(new \DateTime($item['date']), $r['regionID'], $i['typeID']);
                    if ($exists instanceof ItemPrice){

                    } else {
                    */
                    $p = $this->makePriceData($item, $r, $i);

                    $em->persist($p);


                }

                $progress->advance();
            }
            $em->flush();
        }

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
        return "market/$region/types/$item/history/";
    }

}
