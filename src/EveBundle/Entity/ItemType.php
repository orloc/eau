<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_types")
 *
 * @package AppBundle\Entity
 */
class ItemType
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $group_id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $graphic_id;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=16)
     */
    protected $mass;

    /**
     * @ORM\Column(type="integer")
     */
    protected $portion_size;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=16)
     */
    protected $radius;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sound_id;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=16)
     */
    protected $volume;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;
}
