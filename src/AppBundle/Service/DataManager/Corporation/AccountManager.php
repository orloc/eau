<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Entity\Account;
use AppBundle\Entity\AccountBalance;
use AppBundle\Entity\Corporation;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class AccountManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{
    public function updateAccounts(Corporation $corporation)
    {
        $apiKey = $this->getApiKey($corporation);

        $client = $this->getClient($apiKey);

        $accounts = $client->AccountBalance([
            'characterID' => $apiKey->getEveCharacterId(),
        ]);

        $this->mapList($accounts->accounts, ['corp' => $corporation]);
    }

    public function mapList($items, array $options)
    {
        $repo = $this->doctrine->getRepository('AppBundle:Account');
        $corp = isset($options['corp']) ? $options['corp'] : false;

        if (!$corp instanceof Corporation) {
            throw new OptionDefinitionException(sprintf('Option corp required and must by of type %s, got %s', get_class(new Corporation())));
        }

        foreach ($items as $a) {
            $exists = $repo->findOneBy([
                'corporation' => $corp,
                'division' => $a->accountKey,
            ]);

            if (!$exists instanceof Account) {
                $account = new Account();
                $account->setEveAccountId($a->accountID)
                    ->setDivision($a->accountKey);
            } else {
                $account = $exists;
            }

            $balance = $this->mapItem($a);

            $account->addBalance($balance);

            if (!$exists instanceof Account) {
                $options['corp']->addAccount($account);
            }
        }
    }

    public function mapItem($item)
    {
        $balance = new AccountBalance();
        $balance->setBalance($item->balance);

        return $balance;
    }

    public function updateLatestBalances(array $accounts, $date = false)
    {
        $balanceRepo = $this->doctrine->getRepository('AppBundle:AccountBalance');
        foreach ($accounts as $acc) {
            if ($date) {
                $balance = $balanceRepo->getLatestBalance($acc, $date);
                if ($balance !== null) {
                    $balance = $balance->getBalance();
                }
            } else {
                $balance = $balanceRepo->getLatestBalance($acc);

                if ($balance !== null) {
                    $balance = $balance->getBalance();
                }
            }

            $lastDay = ($b = $balanceRepo->getLastDayBalance($acc)) instanceof AccountBalance
                ? $b->getBalance()
                : 0;

            $acc->setCurrentBalance($balance)
                ->setLastDayBalance($lastDay);
        }
    }

    public static function getName()
    {
        return 'account_manager';
    }
}
