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
        $assetManager = $this->getContainer()->get('app.asset.manager');

        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        foreach ($corps as $c){
            if ($c->getEveId() === null){
                $result = $corpManager->getCorporationDetails($c);

                $c->setName($result['name'])
                    ->setEveId($result['id']);


                $em->persist($c);
                $em->flush();

            }

            try {
                $corpManager->updateAccounts($c);
                $corpManager->updateJournalTransactions($c);
                $corpManager->updateMarketTransactions($c);
                $c->setLastUpdatedAt(new \DateTime());

                $em->persist($c);
                $em->flush();
            } catch (\Exception $e){
                $this->getContainer()->get('logger')->error(sprintf("Error syncing data for %s with API KEY %s and messages: %s", $c->getName(), $c->getApiCredentials()->getId(), $e->getMessage()));
            }

            //$assetManager->generateAssetList($c);


        }

    }
}