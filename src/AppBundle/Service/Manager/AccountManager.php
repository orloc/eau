<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class AccountManager implements DataManagerInterface, MappableDataManagerInterface {

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

        $this->mapList($accounts, ['corp' => $corporation]);

    }

    public function mapList($items, array $options){
        $repo = $this->doctrine->getRepository('AppBundle:Account');
        $corp = isset($options['corp']) ? $options['corp'] : false;

        if (!$corp instanceof Corporation){
            throw new OptionDefinitionException(sprintf('Option corp required and must by of type %s, got %s', get_class(new Corporation())));
        }

        foreach ($items as $a){
            $exists = $repo->findOneBy([
                'corporation' => $corp,
                'division' => $a->accountKey
            ]);

            if (!$exists instanceof Account){
                $account = new Account();
                $account->setEveAccountId($a->accountID)
                    ->setDivision($a->accountKey);
            } else {
                $account = $exists;
            }

            $balance = $this->mapItem($a);

            $account->addBalance($balance);

            if (!$exists instanceof Account){
                $options['corp']->addAccount($account);
            }
        }

    }

    public function mapItem($item){
        $balance = new AccountBalance();
        $balance->setBalance($item->balance);

        return $balance;

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

    public function getClient(ApiCredentials $key, $scope = 'corp'){

        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }

}
