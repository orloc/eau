<?php

namespace AppBundle\Command;

use AppBundle\Entity\ApiUpdate;
use AppBundle\Service\DataManager\Eve\ConquerableStationManager;
use AppBundle\Service\DataManager\Eve\RefTypeManager;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCorporationDataCommand extends ContainerAwareCommand
{
    protected $log;
    protected $em;
    protected $update_service;
    protected $evedata_registry;
    protected $now;

    private function _setup(){
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->log = $this->getContainer()->get('logger');
        $this->update_service = $this->getContainer()->get('app.evedataupdate.service');
        $this->evedata_registry = $this->getContainer()->get('app.evedata.registry');
        $this->now = new Carbon();
    }

    protected function configure()
    {
        $this
            ->setName('evetool:update_corp')
            ->addOption('force', InputOption::VALUE_OPTIONAL)
            ->setDescription('Updates Corporations in the database with the most recent data based on individual cache timers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_setup();
        $force = $input->getOption('force', false);

        $this->log->info('Preparing Reference Data');
        $update = $this->updateConquerableStations() || $this->updateRefTypes();

        if ($update) {
            $this->log->info('Flushing Data');
            $this->em->flush();
        }

        $corps = $this->em->getRepository('AppBundle:Corporation')
            ->findAll();

        foreach ($corps as $c) {
            if ($c->getCorporationDetails()){
                $this->log->info("Starting Update {$c->getCorporationDetails()->getName()}\n\n");
            } else {
                $this->log->info("Starting New Corp\n\n");
            }
            try {
                $changed = $this->getContainer()->get('app.corporation.manager')->checkCorporationDetails($c);

                if ($changed){
                    $this->em->flush();
                }

                $this->update_service->updateShortTimerCalls($c, $force);
                $this->em->flush();
                $this->update_service->updateLongTimerCalls($c, $force);
                $this->em->flush();
                $this->log->info("Finished Updated {$c->getCorporationDetails()->getName()}");
            } catch (\Exception $e) {
                $this->log->info("ERROR: {$e->getMessage()}");
            }
        }
        $this->log->info('Updating Compound Fields.');
        $start = microtime(true);
        $this->update_service->updateAssetCache($corps, $force);
        $this->log->info(sprintf('Done in %s', microtime(true) - $start));
    }

    protected function updateConquerableStations(){
        $stationLastUpdate = $this->em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::CONQUERABLE_STATIONS);

        $update = false;
        if ($stationLastUpdate === null || $this->now->diffInHours(Carbon::instance($stationLastUpdate->getCreatedAt())) > 24) {
            $this->log->info('Updating Conquerable Stations');
            $this->evedata_registry->get(ConquerableStationManager::getName())
                ->updateConquerableStations();

            $update = $this->update_service->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::CONQUERABLE_STATIONS, true);

            $this->em->persist($update);
            $update = true;
        }

        return $update;
    }

    protected function updateRefTypes(){
        $refTypeLastUpdate = $this->em->getRepository('AppBundle:ApiUpdate')
            ->getLastUpdateByType(ApiUpdate::REF_TYPES);

        $update = false;
        if ($refTypeLastUpdate === null || $this->now->diffInHours(Carbon::Instance($refTypeLastUpdate->getCreatedAt())) > 24) {
            $this->log->info('Updating Reference Types');
            $this->evedata_registry->get(RefTypeManager::getName())
                ->updateRefTypes();

            $update = $this->update_service->createApiUpdate(ApiUpdate::CACHE_STYLE_LONG, ApiUpdate::REF_TYPES, true);

            $this->em->persist($update);
            $update = true;
        }

        return $update;
    }
}
