<?php

namespace AppBundle\Service;

use AppBundle\Entity\ApiUpdate;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\CorporationDetail;
use AppBundle\Service\DataManager\AccountManager;
use AppBundle\Service\DataManager\AssetManager;
use AppBundle\Service\DataManager\CorporationManager;
use AppBundle\Service\DataManager\JournalTransactionManager;
use AppBundle\Service\DataManager\MarketOrderManager;
use AppBundle\Service\DataManager\MarketTransactionManager;
use AppBundle\Service\DataManager\StarbaseManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;

class EveDataUpdateService {

    protected $doctrine;

    protected $registry;

    protected $log;

    public function __construct(DataManagerRegistry $registry, Registry $doctrine, Logger $log){
        $this->doctrine = $doctrine;
        $this->registry = $registry;
        $this->log = $log;
    }

    public function updateShortTimerCalls(Corporation $c, $force = false){
        $calls = [
            AccountManager::getName() => 'updateAccounts',
            CorporationManager::getName() => ['getCorporationSheet', 'getMembers'],
            JournalTransactionManager::getName() => 'updateJournalTransactions',
            MarketTransactionManager::getName() => 'updateMarketTransactions',
            StarbaseManager::getName() => 'getStarbases'
        ];

        foreach ($calls as $manager => $call){
            if (is_array($call)){
                foreach ($call as $ic){
                    if(!$this->checkShortTimer($c, $this->resolveCall($ic)) || $force === true) {
                        $this->doUpdate($manager, $ic, $c, ApiUpdate::CACHE_STYLE_SHORT);
                    }
                }
            } else {
                if(!$this->checkShortTimer($c, $this->resolveCall($call)) || $force === true) {
                    $this->doUpdate($manager, $call, $c, ApiUpdate::CACHE_STYLE_SHORT);
                }
            }
        }
    }

    public function updateLongTimerCalls(Corporation $c, $force = false){
        $calls = [
            AssetManager::getName() => 'generateAssetList',
            MarketOrderManager::getName() => 'getMarketOrders'
        ];

        foreach ($calls as $manager => $call){
            if(!$this->checkLongTimer($c, $this->resolveCall($call)) || $force === true){
                $this->doUpdate($manager, $call, $c, ApiUpdate::CACHE_STYLE_LONG);
            }
        }

    }

    public function checkShortTimer(Corporation $c, $call){
        return $this->doctrine->getRepository('AppBundle:ApiUpdate')
            ->getShortTimerExpired($c, $call);
    }

    public function checkLongTimer(Corporation $c, $call){
        return $this->doctrine->getRepository('AppBundle:ApiUpdate')
            ->getLongTimerExpired($c, $call);
    }

    public function updateAssetCache(array $c){
        $this->registry->get(AssetManager::getName())
            ->updateAssetGroupCache($c);
    }


    public function createApiUpdate($type, $call, $success, Corporation $corp = null){
        $access = new ApiUpdate();

        $access->setType($type)
            ->setApiCall($call)
            ->setSucceeded($success);

        if ($corp){
            $access->setCorporation($corp);
        }

        return $access;
    }

    protected function doUpdate($manager, $call, Corporation $c, $cache_style){
        $this->log->info(sprintf("Executing %s", $call));
        $start = microtime(true) ;
        $success = $this->tryCall($manager, $call, $c);

        $em = $this->doctrine->getManager();

        $update = $this->createApiUpdate(
            $cache_style,
            $this->resolveCall($call),
            $success,
            $c
        );

        $c->addApiUpdate($update);

        $em->persist($c);

        $end = microtime(true) - $start;
        $this->log->info(sprintf("Done Executing %s in %s seconds", $call, $end));
    }

    protected function tryCall($manager, $function, $arg){
        try {
            $this->registry
                ->get($manager)
                ->$function($arg);

            return true;
        } catch (\Exception $e){
            $this->log->error(sprintf("Error syncing data for %s  on call %s with: %s",
                $arg->getCorporationDetails()->getName(),
                $function,
                $e->getMessage()
            ));

            return false;
        }
    }

    protected function resolveCall($call){
        switch ($call){
            case 'updateAccounts':
                return ApiUpdate::CORP_ACC_BALANCES;
            case 'updateJournalTransactions':
                return ApiUpdate::CORP_WALLET_JOURNAL;
            case 'updateMarketTransactions':
                return ApiUpdate::CORP_WALLET_TRANSACTION;
            case 'generateAssetList':
                return ApiUpdate::CORP_ASSET_LIST;
            case 'getMarketOrders':
                return ApiUpdate::CORP_MARKET_ORDERS;
            case 'getStarbases':
                return ApiUpdate::CORP_STARBASE_LIST;
            case 'getMembers':
                return ApiUpdate::CORP_MEMBERS;
            case 'getCorporationSheet':
                return ApiUpdate::CORP_DETAILS;
            case 'updateRefTypes':
                return ApiUpdate::REF_TYPES;
            case 'updateConquerableStations':
                return ApiUpdate::CONQUERABLE_STATIONS;
        }
    }
}
