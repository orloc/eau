<?php

namespace AppBundle\Service\Manager;

interface MappableDataManagerInterface {

    public function mapList($items, array $params);

    public function mapItem($item);
}
