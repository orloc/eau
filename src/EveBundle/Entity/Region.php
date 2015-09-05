<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="regions")
 * @package AppBundle\Entity
 */
class Region
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $region_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

}
