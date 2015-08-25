<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCorporationDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('evetool:update_corp')
            ->setDescription('Updates Corporations in the database with the most recent data based on individual cache timers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $corpManager = $this->getContainer()->get('app.corporation.manager');

        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        foreach ($corps as $c){
            if ($c->getEveId() === null){
                $result = $corpManager->getCorporationDetails($c);

                $c->setName($result['name'])
                    ->setEveId($result['id']);

                $corpManager->generateAccounts($c);

                $em->persist($c);
                $em->flush();

            } else {
                $corpManager->updateAccounts($c);
            }

            $corpManager->updateJournalTransactions($c);

            $em->persist($c);
            $em->flush();
        }

    }
}