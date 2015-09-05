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

            foreach ($regions as $r){
                $this->createRegion($r);
            }




        }



    }

    private function createRegion(array $data){

        var_dump($data);die;

        $item = new Region();


        return $item;
    }

}
