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

        $force = $input->getOption('force');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dataUpdateService = $this->getContainer()->get('app.evedataupdate.service');

        $updateRegistry = $this->getContainer()->get('app.evedata.registry');

        $corps = $em->getRepository('AppBundle:Corporation')
            ->findAll();

        $now = new Carbon();

        $stationLastUpdate = $em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::CONQUERABLE_STATIONS);

        $refTypeLastUpdate = $em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::REF_TYPES);


        if ($stationLastUpdate === null || $now->diff(Carbon::instance($stationLastUpdate->getCreatedAt()))) {
            $updateRegistry->get(ConquerableStationManager::getName())
                ->updateConquerableStations();

            $update = $dataUpdateService->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::CONQUERABLE_STATIONS, true);

            $em->persist($update);
        }

        if ($refTypeLastUpdate === null || $now->diff(Carbon::instance($refTypeLastUpdate->getCreatedAt()))) {
            $updateRegistry->get(RefTypeManager::getName())
                ->updateRefTypes();

            $update = $dataUpdateService->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::REF_TYPES, true);

            $em->persist($update);
        }

        $em->flush();

        foreach ($corps as $c){
            $dataUpdateService->checkCorporationDetails($c);

            $dataUpdateService->updateShortTimerCalls($c, $force);
            $dataUpdateService->updateLongTimerCalls($c, $force);

            $dataUpdateService->updateAssetCache($c);
        }
    }
}