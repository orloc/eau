<?php

namespace EveBundle\Command;

use EveBundle\Entity\Region;
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



    }

    private function createRegion(array $data){

        $item = new Region();


        return $item;
    }

}
