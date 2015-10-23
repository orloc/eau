<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Account;
use AppBundle\Entity\Asset;
use Doctrine\Bundle\DoctrineBundle\Registry;
use EveBundle\Entity\AveragePrice;
use EveBundle\Repository\Registry as EveRegistry;

abstract class AbstractManager {

    protected $doctrine;

    protected $registry;

    public function __construct(Registry $doctrine, EveRegistry $registry){
        $this->doctrine = $doctrine;
        $this->registry = $registry;
    }

    public function buildTransactionParams(Account $acc, $fromID = null){
        $params =  [
            'accountKey' => $acc->getDivision(),
            'rowCount' => 2000
        ];

        if ($fromID){
            $params = array_merge($params, [ 'fromID' => $fromID]);
        }

        return $params;
    }

}