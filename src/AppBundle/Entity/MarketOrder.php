<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarketOrderRepository")
 * @ORM\Table(name="market_orders", uniqueConstraints={
 * })
 * @JMS\ExclusionPolicy("all")
 */
///  @ORM\UniqueConstraint(name="datePlacedAt_indx", columns={"placed_by_id", "issued","market_order_group_id", "type_id", "placed_at_id"})
class MarketOrder
{
    const OPEN = 0,
          CLOSED = 1,
          ENDED = 2,
          CANCELED = 3,
          PENDING = 4,
          DELETED = 5;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $order_id;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $placed_by_id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MarketOrderGroup", inversedBy="market_orders")
     */
    protected $market_order_group;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $placed_at_id;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $type_id;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $state;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $total_volume;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $volume_remaining;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $order_range;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $account_key;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $duration;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     * @JMS\Expose()
     */
    protected $escrow;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     * @JMS\Expose()
     */
    protected $price;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose()
     */
    protected $bid;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $issued;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @JMS\Expose()
     */
    protected $descriptors;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getDescriptors()
    {
        return $this->descriptors;
    }

    public function setDescriptors(array $val)
    {
        $this->descriptors = $val;

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function getNiceState()
    {
        switch ($this->getState()) {
            case self::OPEN: return 'Open';
            case self::ENDED: return 'Ended';
            case self::CANCELED: return 'Canceled';
            case self::CLOSED: return 'Closed';
            case self::PENDING: return 'Pending';
            case self::DELETED: return 'Deleted';
        }
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set placed_by_id.
     *
     * @param int $placedById
     *
     * @return MarketOrder
     */
    public function setPlacedById($placedById)
    {
        $this->placed_by_id = intval($placedById);

        return $this;
    }

    /**
     * Get placed_by_id.
     *
     * @return int
     */
    public function getPlacedById()
    {
        return $this->placed_by_id;
    }

    /**
     * Set placed_at_id.
     *
     * @param int $placedAtId
     *
     * @return MarketOrder
     */
    public function setPlacedAtId($placedAtId)
    {
        $this->placed_at_id = intval($placedAtId);

        return $this;
    }

    /**
     * Get placed_at_id.
     *
     * @return int
     */
    public function getPlacedAtId()
    {
        return $this->placed_at_id;
    }

    /**
     * Set type_id.
     *
     * @param int $typeId
     *
     * @return MarketOrder
     */
    public function setTypeId($typeId)
    {
        $this->type_id = intval($typeId);

        return $this;
    }

    /**
     * Get type_id.
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return MarketOrder
     */
    public function setState($state)
    {
        $this->state = intval($state);

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set total_volume.
     *
     * @param int $totalVolume
     *
     * @return MarketOrder
     */
    public function setTotalVolume($totalVolume)
    {
        $this->total_volume = intval($totalVolume);

        return $this;
    }

    /**
     * Get total_volume.
     *
     * @return int
     */
    public function getTotalVolume()
    {
        return $this->total_volume;
    }

    /**
     * Set volume_remaining.
     *
     * @param int $volumeRemaining
     *
     * @return MarketOrder
     */
    public function setVolumeRemaining($volumeRemaining)
    {
        $this->volume_remaining = intval($volumeRemaining);

        return $this;
    }

    /**
     * Get volume_remaining.
     *
     * @return int
     */
    public function getVolumeRemaining()
    {
        return $this->volume_remaining;
    }

    /**
     * Set range.
     *
     * @param int $range
     *
     * @return MarketOrder
     */
    public function setOrderRange($range)
    {
        $this->order_range = intval($range);

        return $this;
    }

    /**
     * Get range.
     *
     * @return int
     */
    public function getOrderRange()
    {
        return $this->order_range;
    }

    /**
     * Set account_key.
     *
     * @param int $accountKey
     *
     * @return MarketOrder
     */
    public function setAccountKey($accountKey)
    {
        $this->account_key = intval($accountKey);

        return $this;
    }

    /**
     * Get account_key.
     *
     * @return int
     */
    public function getAccountKey()
    {
        return $this->account_key;
    }

    /**
     * Set duration.
     *
     * @param int $duration
     *
     * @return MarketOrder
     */
    public function setDuration($duration)
    {
        $this->duration = intval($duration);

        return $this;
    }

    /**
     * Get duration.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set escrow.
     *
     * @param string $escrow
     *
     * @return MarketOrder
     */
    public function setEscrow($escrow)
    {
        $this->escrow = floatval($escrow);

        return $this;
    }

    /**
     * Get escrow.
     *
     * @return string
     */
    public function getEscrow()
    {
        return $this->escrow;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return MarketOrder
     */
    public function setPrice($price)
    {
        $this->price = floatval($price);

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set bid.
     *
     * @param bool $bid
     *
     * @return MarketOrder
     */
    public function setBid($bid)
    {
        $this->bid = boolval($bid);

        return $this;
    }

    /**
     * Get bid.
     *
     * @return bool
     */
    public function getBid()
    {
        return $this->bid;
    }

    /**
     * Set issued.
     *
     * @param \DateTime $issued
     *
     * @return MarketOrder
     */
    public function setIssued(\DateTime $issued)
    {
        $this->issued = $issued;

        return $this;
    }

    /**
     * Get issued.
     *
     * @return \DateTime
     */
    public function getIssued()
    {
        return $this->issued;
    }

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     *
     * @return MarketOrder
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set order_id.
     *
     * @param int $orderId
     *
     * @return MarketOrder
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set market_order_group.
     *
     * @param \AppBundle\Entity\MarketOrderGroup $marketOrderGroup
     *
     * @return MarketOrder
     */
    public function setMarketOrderGroup(\AppBundle\Entity\MarketOrderGroup $marketOrderGroup = null)
    {
        $this->market_order_group = $marketOrderGroup;

        return $this;
    }

    /**
     * Get market_order_group.
     *
     * @return \AppBundle\Entity\MarketOrderGroup
     */
    public function getMarketOrderGroup()
    {
        return $this->market_order_group;
    }
}
