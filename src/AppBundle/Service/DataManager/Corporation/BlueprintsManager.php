<?php

namespace AppBundle\Service\DataManager\Corporation;

use AppBundle\Service\DataManager\AbstractManager;
use AppBundle\Service\DataManager\DataManagerInterface;
use AppBundle\Service\DataManager\MappableDataManagerInterface;

class BlueprintsManager extends AbstractManager implements DataManagerInterface, MappableDataManagerInterface
{
    public function mapList($items, array $options)
    {
    }

    public function mapItem($item)
    {
    }

    public static function getName()
    {
        return 'blueprint_manager';
    }
}
