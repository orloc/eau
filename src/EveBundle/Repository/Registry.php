<?php

namespace EveBundle\Repository;

use Symfony\Component\Debug\Exception\ClassNotFoundException;

class Registry {

    protected $repositories;

    public function set(RepositoryInterface $repo){
        if (!isset($this->repositories[$repo->getName()])) {
            $this->repositories[$repo->getName()] = $repo;
        }

        return $this;
    }

    public function get($name){
        if (isset($this->repositories[$name])){
            return $this->repositories[$name];
        }

        throw new ClassNotFoundException(sprintf("%s repository ws not found", $name), null);
    }
}