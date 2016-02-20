<?php

namespace EveBundle\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractDbalRepository
{
    protected $conn;

    public function __construct(Connection $dbal)
    {
        $this->conn = $dbal;
    }
}
