<?php

namespace AppBundle\Service\Manager;

use AppBundle\Entity\ApiCredentials;

interface DataManagerInterface {

    public function getClient(ApiCredentials $corporation, $scope = null);
}