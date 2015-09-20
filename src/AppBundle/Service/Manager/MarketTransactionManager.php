<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Account;
use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Entity\MarketTransaction;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Repository\Registry as EveRegistry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class MarketTransactionManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    protected $pheal;

    protected $log;

    public function __construct(PhealFactory $pheal, EveRegistry $registry, Registry $doctrine, Logger $log)
    {
        parent::__construct($doctrine, $registry);
        $this->pheal = $pheal;
        $this->log = $log;
    }

    public function updateMarketTransactions(Corporation $corporation, $fromID = null) {
        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($corporation);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $corporation->getId() .' found');

        }
        $client = $this->getClient($apiKey);

        $accounts = $corporation->getAccounts();

        foreach($accounts as $acc){
            $this->log->debug(sprintf("Processing account %s for %s", $acc->getDivision(), $corporation->getName()));

            $params = $this->buildTransactionParams($acc, $fromID);

            $transactions = $client->WalletTransactions($params);

            $this->mapList($transactions, [ 'corp' => $corporation, 'acc' => $acc]);

        }
    }

    public function mapList($items, array $options) {
        $corp = isset($options['corp']) ? $options['corp'] : false;
        $acc = isset($options['acc']) ? $options['acc']: false;

        if (!$corp instanceof Corporation || !$acc instanceof Account) {
            throw new OptionDefinitionException(sprintf('Option corp required and must by of type %s', get_class(new Corporation())));
        }

        foreach ($items->transactions as $t){
            $exists = $this->doctrine->getRepository('AppBundle:MarketTransaction')
                ->hasTransaction($acc, $t->transactionID, $t->journalTransactionID);

            if ($exists === null){
                $trans = $this->mapItem($t);
                $acc->addMarketTransaction($trans);

            } else  {
                $this->log->info(sprintf("Conflicting Market Transaction %s for %s %s", $t->transactionID, $acc->getDivision(), $corp->getName()));
            }
        }
    }

    public function mapItem($item){
        $trans = new MarketTransaction();
        $trans->setDate(new \DateTime($item->transactionDateTime))
            ->setTransactionId($item->transactionID)
            ->setQuantity($item->quantity)
            ->setItemName($item->typeName)
            ->setItemId($item->typeID)
            ->setPrice($item->price)
            ->setClientId($item->clientID)
            ->setClientName($item->clientName)
            ->setCharacterId($item->characterID)
            ->setCharacterName($item->characterName)
            ->setStationId($item->stationID)
            ->setStationName($item->stationName)
            ->setTransactionType($item->transactionType)
            ->setTransactionFor($item->transactionFor)
            ->setJournalTransactionId($item->journalTransactionID)
            ->setClientTypeId($item->clientTypeID);

        return $trans;
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