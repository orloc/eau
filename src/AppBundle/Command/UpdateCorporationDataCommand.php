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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $corpManager = $this->getContainer()->get('app.corporation.manager');
        $marketOrderManager = $this->getContainer()->get('app.marketorder.manager');
        $assetManager = $this->getContainer()->get('app.asset.manager');

        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        $force = $input->getOption('force');

        foreach ($corps as $c){
            if ($c->getEveId() === null){
                $result = $corpManager->getCorporationDetails($c);

                $c->setName($result['name'])
                    ->setEveId($result['id']);


                $em->persist($c);
                $em->flush();

            }
            $short = $em->getRepository('AppBundle:ApiUpdate')
                ->getShortTimerExpired($c);

            $long = $em->getRepository('AppBundle:ApiUpdate')
                ->getLongTimerExpired($c);

            if (!$short || $force === true) {
                try {
                    $corpManager->updateAccounts($c);
                    $corpManager->updateJournalTransactions($c);
                    $corpManager->updateMarketTransactions($c);

                    $c->addApiUpdate(
                        $this->createAccess(ApiUpdate::CACHE_STYLE_SHORT));

                    $em->persist($c);
                    $em->flush();

                } catch (\Exception $e){
                    $this->getContainer()->get('logger')->error(sprintf("Error syncing data for %s with API KEY %s and messages: %s", $c->getName(), $c->getApiCredentials()->getId(), $e->getMessage()));

                }

            }

            if (!$long || $force === true){
                try {
                    $assetManager->generateAssetList($c);
                    $marketOrderManager->getMarketOrders($c);

                    $c->addApiUpdate(
                        $this->createAccess(ApiUpdate::CACHE_STYLE_LONG));

                    $em->persist($c);
                    $em->flush();

                } catch (\Exception $e){
                    $this->getContainer()->get('logger')->error(sprintf("Error syncing data for %s with API KEY %s and messages: %s", $c->getName(), $c->getApiCredentials()->getId(), $e->getMessage()));
                }

            }

        }

    }


    protected function createAccess($type){
        $access = new ApiUpdate();

        $access->setType($type);

        return $access;
    }
}