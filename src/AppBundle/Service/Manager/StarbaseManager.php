<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use Doctrine\Bundle\DoctrineBundle\Registry;
use \EveBundle\Repository\Registry as EveRegistry;
use Tarioch\PhealBundle\DependencyInjection\PhealFactory;

class StarbaseManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{

    protected $pheal;

    public function __construct(PhealFactory $pheal, EveRegistry $registry, Registry $doctrine)
    {
        parent::__construct($doctrine, $registry);
        $this->pheal = $pheal;
    }

    public function getStarbases(Corporation $c){

        $apiKey = $this->doctrine->getRepository('AppBundle:ApiCredentials')
            ->getActiveKey($c);

        if ($apiKey === null){
            throw new \Exception('No active api key for corp' . $c->getId() .' found');
        }

        $client = $this->getClient($apiKey);

        $bases = $client->StarbaseList();

        $this->mapList($bases);
    }

    public function mapList($items, array $options = []){

        var_dump($items);

        die;
    }

    public function mapItem($item){

    }

    public function getClient(ApiCredentials $credentials, $scope = null){
        $client = $this->pheal->createEveOnline(
            $credentials->getApiKey(),
            $credentials->getVerificationCode()
        );

        $client->scope = $scope;

        return $client;
    }
}
