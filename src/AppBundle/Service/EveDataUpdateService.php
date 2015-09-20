<?php

namespace AppBundle\Service;

use AppBundle\Entity\ApiUpdate;
use AppBundle\Entity\Corporation;
use AppBundle\Service\Manager\AccountManager;
use AppBundle\Service\Manager\AssetManager;
use AppBundle\Service\Manager\CorporationManager;
use AppBundle\Service\Manager\JournalTransactionManager;
use AppBundle\Service\Manager\MarketOrderManager;
use AppBundle\Service\Manager\MarketTransactionManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;

class EveDataUpdateService {

    protected $doctrine;

    protected $corp_manager;

    protected $acc_manager;

    protected $marketorder_manager;

    protected $asset_manager;

    protected $journal_manager;

    protected $transaction_manager;

    protected $log;

    public function __construct(CorporationManager $cMan, AccountManager $aMan, MarketOrderManager $moMan, AssetManager $aMan, JournalTransactionManager $jMan, MarketTransactionManager $mtMan, Registry $doctrine, Logger $log){
        $this->doctrine = $doctrine;
        $this->corp_manager = $cMan;
        $this->acc_manager = $aMan;
        $this->marketorder_manager = $moMan;
        $this->asset_manager =  $aMan;
        $this->journal_manager = $jMan;
        $this->transaction_manager = $mtMan;
        $this->log = $log;
    }

    public function checkCorporationDetails(Corporation $c){
        if ($c->getEveId() === null){
            $result = $this->corp_manager->getCorporationDetails($c);

            $c->setName($result['name'])
                ->setEveId($result['id']);

            $em = $this->doctrine->getManager();

            $em->persist($c);
            $em->flush();
        }
    }

    public function updateShortTimerCalls(Corporation $c, $force = false){
        $calls = [
            'acc_manager' => 'updateAccounts',
            'journal_manager' => 'updateJournalTransactions',
            'transaction_manager' => 'updateMarketTransactions'
        ];

        foreach ($calls as $manager => $call){
            if(!$this->checkShortTimer($c, $call) || $force === true) {
                $this->doUpdate($manager, $call, $c, ApiUpdate::CACHE_STYLE_SHORT);
            }
        }
    }

    public function updateLongTimerCalls(Corporation $c, $force = false){
        $calls = [
            'asset_manager' => 'generateAssetList',
            'marketorder_manager' => ''
        ];

        foreach ($calls as $manager => $call){
            if(!$this->checkLongTimer($c, $call) || $force === true){
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


    public function createApiUpdate($type, $call, $success){
        $access = new ApiUpdate();

        $access->setType($type)
            ->setApiCall($call)
            ->setSucceeded($success);

        return $access;
    }

    protected function doUpdate($manager, $call, Corporation $c, $cache_style){
        $success = $this->tryCall($manager, $call, $c);

        $em = $this->doctrine->getManager();

        $update = $this->createApiUpdate(
            $cache_style,
            $this->resolveCall($call),
            $success
        );

        $c->addApiUpdate($update);

        $em->persist($c);
        $em->flush();
    }

    protected function tryCall($manager, $function, $arg){
        try {
            $this->$manager->$function($arg);

            return true;
        } catch (\Exception $e){
            $this->log->error(sprintf("Error syncing data for %s with: %s", $arg->getName(), $e->getMessage()));

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
        }
    }
}
