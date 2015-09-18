<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Exception\InvalidExpirationException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AccountManager {

    private $pheal;

    private $doctrine;

    public function __construct(PhealFactory $pheal, Registry $doctrine){
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
    }

    public function updateAccounts(Corporation $corporation){
        $client = $this->getClient($corporation);

        $accounts = $client->AccountBalance([
            'characterID' => $corporation->getApiCredentials()->getCharacterId()
        ])->accounts;
        $repo = $this->registry->getRepository('AppBundle:Account');

        foreach ($accounts as $a){
            $exists = $repo->findOneBy([
                'corporation' => $corporation,
                'division' => $a->accountKey
            ]);

            if (!$exists instanceof Account){
                $account = new Account();
                $account->setEveAccountId($a->accountID)
                    ->setDivision($a->accountKey);
            } else {
                $account = $exists;
            }

            $balance = new AccountBalance();
            $balance->setBalance($a->balance);

            $account->addBalance($balance);

            if (!$exists instanceof Account){
                $corporation->addAccount($account);
            }
        }
    }

    public function updateLatestBalances(array $accounts){
        $balanceRepo = $this->doctrine->getRepository('AppBundle:AccountBalance');
        foreach($accounts as $acc){
            $balance = $balanceRepo->getLatestBalance($acc)
                ->getBalance();

            $lastDay = ($b = $balanceRepo->getLastDayBalance($acc)) instanceof AccountBalance
                ? $b->getBalance()
                : 0;

            $acc->setCurrentBalance($balance)
                ->setLastDayBalance($lastDay);
        }
    }


    protected function getClient(ApiCredentials $entity){
        return $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
    }
}
