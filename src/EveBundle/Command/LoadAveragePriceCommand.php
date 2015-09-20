<?php

namespace EveBundle\Command;

use EveBundle\Entity\AveragePrice;
use EveBundle\Entity\Region;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadAveragePriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evedata:load_averageprices')
            ->setDescription('Loads average prices from the crest API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $registry = $this->getContainer()->get('doctrine');
        $em = $registry->getManager('eve_data');

        $client = new Client();

        $result = $client->get('https://public-crest.eveonline.com/market/prices/');

        if ($result->getStatusCode() === 200){

            $json = $result->getBody()->getContents();

            $data = $this->getContainer()->get('jms_serializer')->deserialize($json, 'array','json');

            $prices = $data['items'];

            $em = $this->getContainer()->get('doctrine')->getManager('eve_data');

            foreach ($prices as $p){
                $e = $this->createAvgPrice($p);
                $em->persist($e);
            }

            $em->flush();

        }
    }

    private function createAvgPrice(array $data){
        $item = new AveragePrice();

        $item->setTypeId($data['type']['id'])
            ->setAdjustedPrice(isset($data['adjustedPrice']) ? $data['adjustedPrice']: null)
            ->setAveragePrice(isset($data['averagePrice'])  ? $data['averagePrice']: null);

        return $item;
    }

}
