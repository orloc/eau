<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EveBundle\Repository\ItemTypeRepository")
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
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $group_id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $market_group_id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $graphic_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mass;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $portion_size;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
     */
    protected $radius;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
     */
    protected $base_price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sound_id;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2, nullable=true)
     */
    protected $volume;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ItemType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set group_id
     *
     * @param integer $groupId
     * @return ItemType
     */
    public function setGroupId($groupId)
    {
        $this->group_id = $groupId;

        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Set graphic_id
     *
     * @param integer $graphicId
     * @return ItemType
     */
    public function setGraphicId($graphicId)
    {
        $this->graphic_id = $graphicId;

        return $this;
    }

    /**
     * Get graphic_id
     *
     * @return integer 
     */
    public function getGraphicId()
    {
        return $this->graphic_id;
    }

    /**
     * Set mass
     *
     * @param string $mass
     * @return ItemType
     */
    public function setMass($mass)
    {
        $this->mass = $mass;

        return $this;
    }

    /**
     * Get mass
     *
     * @return string 
     */
    public function getMass()
    {
        return $this->mass;
    }

    /**
     * Set portion_size
     *
     * @param integer $portionSize
     * @return ItemType
     */
    public function setPortionSize($portionSize)
    {
        $this->portion_size = $portionSize;

        return $this;
    }

    /**
     * Get portion_size
     *
     * @return integer 
     */
    public function getPortionSize()
    {
        return $this->portion_size;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return ItemType
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set radius
     *
     * @param string $radius
     * @return ItemType
     */
    public function setRadius($radius)
    {
        $this->radius = $radius;

        return $this;
    }

    /**
     * Get radius
     *
     * @return string 
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * Set sound_id
     *
     * @param integer $soundId
     * @return ItemType
     */
    public function setSoundId($soundId)
    {
        $this->sound_id = $soundId;

        return $this;
    }

    /**
     * Get sound_id
     *
     * @return integer 
     */
    public function getSoundId()
    {
        return $this->sound_id;
    }

    /**
     * Set volume
     *
     * @param string $volume
     * @return ItemType
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume
     *
     * @return string 
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ItemType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set market_group_id
     *
     * @param integer $marketGroupId
     * @return ItemType
     */
    public function setMarketGroupId($marketGroupId)
    {
        $this->market_group_id = $marketGroupId;

        return $this;
    }

    /**
     * Get market_group_id
     *
     * @return integer 
     */
    public function getMarketGroupId()
    {
        return $this->market_group_id;
    }

    /**
     * Set base_price
     *
     * @param string $basePrice
     * @return ItemType
     */
    public function setBasePrice($basePrice)
    {
        $this->base_price = $basePrice;

        return $this;
    }

    /**
     * Get base_price
     *
     * @return string 
     */
    public function getBasePrice()
    {
        return $this->base_price;
    }

    /**
     * Set type_id
     *
     * @param integer $typeId
     * @return ItemType
     */
    public function setTypeId($typeId)
    {
        $this->type_id = $typeId;

        return $this;
    }

    /**
     * Get type_id
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->type_id;
    }
}
