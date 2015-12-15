<?php

namespace AppBundle\Command;

use AppBundle\Entity\ApiUpdate;
use AppBundle\Service\DataManager\ConquerableStationManager;
use AppBundle\Service\DataManager\RefTypeManager;
use Carbon\Carbon;
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

        $force = $input->getOption('force', false);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $log = $this->getContainer()->get('logger');
        $dataUpdateService = $this->getContainer()->get('app.evedataupdate.service');

        $updateRegistry = $this->getContainer()->get('app.evedata.registry');


        $log->info('Preparing Reference Data');
        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        $now = new Carbon();

        $stationLastUpdate = $em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::CONQUERABLE_STATIONS);

        $refTypeLastUpdate = $em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::REF_TYPES);

        $update = false;
        if ($stationLastUpdate === null || $now->diffInHours(Carbon::instance($stationLastUpdate->getCreatedAt())) > 24) {
            $log->info('Updating Conquerable Stations');
            $updateRegistry->get(ConquerableStationManager::getName())
                ->updateConquerableStations();

            $update = $dataUpdateService->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::CONQUERABLE_STATIONS, true);

            $em->persist($update);
            $update = true;
        }

        if ($refTypeLastUpdate === null || $now->diffInHours(Carbon::Instance($refTypeLastUpdate->getCreatedAt())) > 24) {
            $log->info('Updating Reference Types');
            $updateRegistry->get(RefTypeManager::getName())
                ->updateRefTypes();

            $update = $dataUpdateService->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::REF_TYPES, true);

            $em->persist($update);
            $update = true;
        }

        if ($update){
            $log->info('Flushing Data');
            $em->flush();
        }

        foreach ($corps as $c){
            $dataUpdateService->checkCorporationDetails($c);

            $dataUpdateService->updateShortTimerCalls($c, $force);
            $dataUpdateService->updateLongTimerCalls($c, $force);

        }
        $dataUpdateService->updateAssetCache($c);
    }
}