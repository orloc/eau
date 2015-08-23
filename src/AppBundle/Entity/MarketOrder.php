<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarketOrder
 */
class MarketOrder
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $placed_by_id;

    /**
     * @var integer
     */
    private $placed_at_id;

    /**
     * @var integer
     */
    private $type_id;

    /**
     * @var integer
     */
    private $state;

    /**
     * @var integer
     */
    private $total_volume;

    /**
     * @var integer
     */
    private $volume_remaining;

    /**
     * @var integer
     */
    private $range;

    /**
     * @var integer
     */
    private $account_key;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var string
     */
    private $escrow;

    /**
     * @var string
     */
    private $price;

    /**
     * @var boolean
     */
    private $bid;

    /**
     * @var \DateTime
     */
    private $issued;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \AppBundle\Entity\Corporation
     */
    private $corporation;


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
     * Set placed_by_id
     *
     * @param integer $placedById
     * @return MarketOrder
     */
    public function setPlacedById($placedById)
    {
        $this->placed_by_id = $placedById;

        return $this;
    }

    /**
     * Get placed_by_id
     *
     * @return integer 
     */
    public function getPlacedById()
    {
        return $this->placed_by_id;
    }

    /**
     * Set placed_at_id
     *
     * @param integer $placedAtId
     * @return MarketOrder
     */
    public function setPlacedAtId($placedAtId)
    {
        $this->placed_at_id = $placedAtId;

        return $this;
    }

    /**
     * Get placed_at_id
     *
     * @return integer 
     */
    public function getPlacedAtId()
    {
        return $this->placed_at_id;
    }

    /**
     * Set type_id
     *
     * @param integer $typeId
     * @return MarketOrder
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
     * Set state
     *
     * @param integer $state
     * @return MarketOrder
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
     * Set total_volume
     *
     * @param integer $totalVolume
     * @return MarketOrder
     */
    public function setTotalVolume($totalVolume)
    {
        $this->total_volume = $totalVolume;

        return $this;
    }

    /**
     * Get total_volume
     *
     * @return integer 
     */
    public function getTotalVolume()
    {
        return $this->total_volume;
    }

    /**
     * Set volume_remaining
     *
     * @param integer $volumeRemaining
     * @return MarketOrder
     */
    public function setVolumeRemaining($volumeRemaining)
    {
        $this->volume_remaining = $volumeRemaining;

        return $this;
    }

    /**
     * Get volume_remaining
     *
     * @return integer 
     */
    public function getVolumeRemaining()
    {
        return $this->volume_remaining;
    }

    /**
     * Set range
     *
     * @param integer $range
     * @return MarketOrder
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Get range
     *
     * @return integer 
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set account_key
     *
     * @param integer $accountKey
     * @return MarketOrder
     */
    public function setAccountKey($accountKey)
    {
        $this->account_key = $accountKey;

        return $this;
    }

    /**
     * Get account_key
     *
     * @return integer 
     */
    public function getAccountKey()
    {
        return $this->account_key;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return MarketOrder
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set escrow
     *
     * @param string $escrow
     * @return MarketOrder
     */
    public function setEscrow($escrow)
    {
        $this->escrow = $escrow;

        return $this;
    }

    /**
     * Get escrow
     *
     * @return string 
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return MarketOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set bid
     *
     * @param boolean $bid
     * @return MarketOrder
     */
    public function setBid($bid)
    {
        $this->bid = $bid;

        return $this;
    }

    /**
     * Get bid
     *
     * @return boolean 
     */
    public function getBid()
    {
        return $this->bid;
    }

    /**
     * Set issued
     *
     * @param \DateTime $issued
     * @return MarketOrder
     */
    public function setIssued($issued)
    {
        $this->issued = $issued;

        return $this;
    }

    /**
     * Get issued
     *
     * @return \DateTime 
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MarketOrder
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

    /**
     * Set corporation
     *
     * @param \AppBundle\Entity\Corporation $corporation
     * @return MarketOrder
     */
    public function setCorporation(\AppBundle\Entity\Corporation $corporation = null)
    {
        $this->corporation = $corporation;

        return $this;
    }

    /**
     * Get corporation
     *
     * @return \AppBundle\Entity\Corporation 
     */
    public function getCorporation()
    {
        return $this->corporation;
    }
}
