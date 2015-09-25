<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StarBaseRepository")
 * @ORM\Table(name="starbases", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="datePlacedAt_indx", columns={"placed_by_id", "issued", "type_id", "placed_at_id"})
 * })
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Starbase
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $item_id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $moon_id;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $state;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $state_timestamp;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $online_timestamp;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $standing_owner_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct(){
        $this->created_at = new \DateTime();
    }

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
     * Set item_id
     *
     * @param integer $itemId
     * @return Starbase
     */
    public function setItemId($itemId)
    {
        $this->item_id = $itemId;

        return $this;
    }

    /**
     * Get item_id
     *
     * @return integer 
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Set type_id
     *
     * @param integer $typeId
     * @return Starbase
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

    /**
     * Set moon_id
     *
     * @param integer $moonId
     * @return Starbase
     */
    public function setMoonId($moonId)
    {
        $this->moon_id = $moonId;

        return $this;
    }

    /**
     * Get moon_id
     *
     * @return integer 
     */
    public function getMoonId()
    {
        return $this->moon_id;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Starbase
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state_timestamp
     *
     * @param \DateTime $stateTimestamp
     * @return Starbase
     */
    public function setStateTimestamp($stateTimestamp)
    {
        $this->state_timestamp = $stateTimestamp;

        return $this;
    }

    /**
     * Get state_timestamp
     *
     * @return \DateTime 
     */
    public function getStateTimestamp()
    {
        return $this->state_timestamp;
    }

    /**
     * Set online_timestamp
     *
     * @param \DateTime $onlineTimestamp
     * @return Starbase
     */
    public function setOnlineTimestamp($onlineTimestamp)
    {
        $this->online_timestamp = $onlineTimestamp;

        return $this;
    }

    /**
     * Get online_timestamp
     *
     * @return \DateTime 
     */
    public function getOnlineTimestamp()
    {
        return $this->online_timestamp;
    }

    /**
     * Set standing_owner_id
     *
     * @param integer $standingOwnerId
     * @return Starbase
     */
    public function setStandingOwnerId($standingOwnerId)
    {
        $this->standing_owner_id = $standingOwnerId;

        return $this;
    }

    /**
     * Get standing_owner_id
     *
     * @return integer 
     */
    public function getStandingOwnerId()
    {
        return $this->standing_owner_id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Starbase
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
