<?php

namespace EveBundle\Command;

use EveBundle\Entity\Region;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadRegionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evedata:load_regions')
            ->setDescription('Loads regions from the crest API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $registry = $this->getContainer()->get('doctrine');
        $em = $registry->getManager('eve_data');

        $client = new Client();

        $result = $client->get('https://public-crest.eveonline.com/regions/');

        if ($result->getStatusCode() === 200){

            $json = $result->getBody()->getContents();

            $data = $this->getContainer()->get('jms_serializer')->deserialize($json, 'array','json');

            $regions = $data['items'];

            $em = $this->getContainer()->get('doctrine')->getManager('eve_data');

            foreach ($regions as $r){
                $regionEntity = $this->createRegion($r);

                $em->persist($regionEntity);
            }

            $em->flush();

        }
    }

    private function createRegion(array $data){

        $item = new Region();

        $urlPieces = preg_split('/\//', $data['href']);

        $regionId = intval($urlPieces[count($urlPieces)-2]);

        $item->setName($data['name'])
            ->setRegionUrl($data['href'])
        ->setRegionId($regionId);

        return $item;
    }

}
