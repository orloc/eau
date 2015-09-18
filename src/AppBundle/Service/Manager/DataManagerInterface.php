<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\Corporation;

interface DataManagerInterface {

    public function updateResultSet(array $items);

    public function mapList(array $items, Corporation $corp);

    public function mapItem($item);

    public function getClient(Corporation $corporation, $scope = null);
}