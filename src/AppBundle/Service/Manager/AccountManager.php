<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
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

        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($corporation);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $corporation->getId() .' found');
        }

        $client = $this->getClient($apiKey);

        $accounts = $client->AccountBalance([
            'characterID' => $corporation->getApiCredentials()[0]->getCharacterId()
        ]);

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

    public function updateLatestBalances(array $accounts, $date = false){
        $balanceRepo = $this->doctrine->getRepository('AppBundle:AccountBalance');
        foreach($accounts as $acc){
            if ($date){
                $balance = $balanceRepo->getLatestBalance($acc, $date)
                    ->getBalance();
            } else {
                $balance = $balanceRepo->getLatestBalance($acc)
                    ->getBalance();
            }

            $lastDay = ($b = $balanceRepo->getLastDayBalance($acc)) instanceof AccountBalance
                ? $b->getBalance()
                : 0;

            $acc->setCurrentBalance($balance)
                ->setLastDayBalance($lastDay);
        }
    }


    protected function getClient(ApiCredentials $entity)
    {
        return $this->pheal->createEveOnline($entity->getApiKey(), $entity->getVerificationCode());
    }

}
