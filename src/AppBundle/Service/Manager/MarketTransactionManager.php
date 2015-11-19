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

    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry, Logger $log)
    {
        parent::__construct($pheal, $doctrine, $registry);
        $this->log = $log;
    }

    public function updateMarketTransactions(Corporation $corporation, $fromID = null) {
        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $accounts = $corporation->getAccounts();

        foreach($accounts as $acc){
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
                $this->log->info(sprintf("Conflicting Market Transaction %s for %s %s", $t->transactionID, $acc->getDivision(), $corp->getCorporationDetails()->getName()));
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

    public static function getName(){
        return 'market_transaction_manager';
    }

}