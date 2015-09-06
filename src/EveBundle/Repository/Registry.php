<?php

namespace EveBundle\Repository;

class Registry {

    protected $repositories;

    public function set(RepositoryInterface $repo){
        if (!isset($this->repositories[$repo->getName()])) {
            $this->repositories[strtolower($repo->getName())] = $repo;
        }

        return $this;
    }

    public function get($name){

        $name = strtolower($name);
        if (isset($this->repositories[$name])){
            return $this->repositories[$name];
        }

        return false;
    }
}