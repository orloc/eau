<?php

namespace AppBundle\Service;


use AppBundle\Service\Manager\DataManagerInterface;

class DataManagerRegistry {

    protected  $managers = [];

    public function set(DataManagerInterface $value){

        $this->managers[$value::getName()] = $value;
    }

    public function get($key){
        return $this->managers[$key];
    }

    public function has($key){
        return isset($this->managers[$key]);
    }

    public function getAll(){
        return $this->managers;
    }
}