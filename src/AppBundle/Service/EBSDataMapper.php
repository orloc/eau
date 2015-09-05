<?php

namespace AppBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;

class EBSDataMapper {

    public function updateObject($obj, array $data){

        $pa = PropertyAccess::createPropertyAccessor();

        foreach ($data as $k => $d){
            if ($pa->isWritable($obj, $k)){
                $pa->setValue($obj, $k, $d);
            }
        }
    }
}