<?php

namespace AppBundle\Command;

use AppBundle\Entity\ApiUpdate;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCorporationDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evetool:update_corp')
            ->addOption('force', InputOption::VALUE_OPTIONAL)
            ->setDescription('Updates Corporations in the database with the most recent data based on individual cache timers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $force = $input->getOption('force');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dataUpdateService = $this->getContainer()->get('app.evedataupdate.service');

        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        foreach ($corps as $c){

            $dataUpdateService->checkCorporationDetails($c);

            $dataUpdateService->updateShortTimerCalls($c, $force);
            $dataUpdateService->updateLongTimerCalls($c, $force);

        }

    }
}