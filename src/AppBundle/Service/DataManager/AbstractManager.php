<?php

namespace AppBundle\Service\DataManager;

use AppBundle\Entity\Account;
use AppBundle\Entity\Corporation;
use AppBundle\Exception\InvalidApiKeyException;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Entity\ApiCredentials;
use EveBundle\Repository\Registry as EveRegistry;
use Psr\Log\LoggerInterface;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

abstract class AbstractManager
{
    protected $doctrine;

    protected $registry;

    protected $pheal;

    protected $log;

    public function __construct(PhealFactory $pheal, Registry $doctrine, EveRegistry $registry, LoggerInterface $logger)
    {
        $this->pheal = $pheal;
        $this->doctrine = $doctrine;
        $this->registry = $registry;
        $this->log = $logger;
    }

    public function buildTransactionParams(Account $acc, $fromID = null)
    {
        $params = [
            'accountKey' => $acc->getDivision(),
            'rowCount' => 2000,
        ];

        if ($fromID) {
            $params = array_merge($params, ['fromID' => $fromID]);
        }

        return $params;
    }

    public function getApiKey(Corporation $entity)
    {
        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($entity);

        if ($apiKey === null) {
            throw new InvalidApiKeyException(sprintf('No active API KEY for Corp %s, with ID %s', $entity->getCorporationDetails()->getName(), $entity->getId()));
        }

        if ($apiKey->getInvalid()){
            throw new InvalidApiKeyException('Api key is invalid for '.$entity->getId());
        }

        return $apiKey;
    }

    public function getClient(ApiCredentials $key, $scope = 'corp')
    {
        $client = $this->pheal->createEveOnline(
            $key->getApiKey(),
            $key->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
