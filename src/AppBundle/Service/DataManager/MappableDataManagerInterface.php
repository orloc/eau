<?php

namespace AppBundle\Service\DataManager;

interface MappableDataManagerInterface {

    public function mapList($items, array $params);

    public function mapItem($item);
}
