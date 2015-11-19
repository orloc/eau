<?php

namespace AppBundle\Service\Manager;


use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\RefType;
use Doctrine\Bundle\DoctrineBundle\Registry;

class RefTypeManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface {

    public function updateRefTypes(){
        $nullKey = new ApiCredentials();

        $client = $this->getClient($nullKey);

        $response = $client->RefTypes()
            ->toArray();

        $this->mapList($response['result']['refTypes'], []);

    }


    public function mapList($items, array $options){
        $em = $this->doctrine->getManager();
        foreach ($items as $i){
            $exists = $em->getRepository('AppBundle:RefType')->findOneBy(['ref_type_id' => $i['refTypeID']]);
            if (!$exists instanceof RefType){
                $objectItem = $this->mapItem($i);
                $em->persist($objectItem);
            }
        }
    }

    public function mapItem($item){
        $obj = new RefType();

        $obj->setRefTypeId($item['refTypeID'])
            ->setRefTypeName($item['refTypeName']);

        return $obj;
    }

    public function getClient(ApiCredentials $key, $scope = 'eve'){
        $client = $this->pheal->createEveOnline();
        $client->scope = $scope;

        return $client;
    }

    public static function getName(){
        return 'ref_type_manager';
    }

}
