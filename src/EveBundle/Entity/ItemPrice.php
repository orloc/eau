<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EveBundle\Repository\ItemPriceRepository")
 * @ORM\Table(name="item_prices", uniqueConstraints={
 */
class ItemPrice
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
     * @ORM\Column(type="string", length=255)
     */
    protected $region_name;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type_name;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $volume;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $order_count;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $low_price;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $high_price;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $avg_price;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
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
     * Set volume.
     *
     * @param int $volume
     *
     * @return ItemPrice
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * Get volume.
     *
     * @return int
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * Set order_count.
     *
     * @param int $orderCount
     *
     * @return ItemPrice
     */
    public function setOrderCount($orderCount)
    {
        $this->order_count = $orderCount;

        return $this;
    }

    /**
     * Get order_count.
     *
     * @return int
     */
    public function getOrderCount()
    {
        return $this->order_count;
    }

    /**
     * Set low_price.
     *
     * @param string $lowPrice
     *
     * @return ItemPrice
     */
    public function setLowPrice($lowPrice)
    {
        $this->low_price = $lowPrice;

        return $this;
    }

    /**
     * Get low_price.
     *
     * @return string
     */
    public function getLowPrice()
    {
        return $this->low_price;
    }

    /**
     * Set high_price.
     *
     * @param string $highPrice
     *
     * @return ItemPrice
     */
    public function setHighPrice($highPrice)
    {
        $this->high_price = $highPrice;

        return $this;
    }

    /**
     * Get high_price.
     *
     * @return string
     */
    public function getHighPrice()
    {
        return $this->high_price;
    }

    /**
     * Set avg_price.
     *
     * @param string $avgPrice
     *
     * @return ItemPrice
     */
    public function setAvgPrice($avgPrice)
    {
        $this->avg_price = $avgPrice;

        return $this;
    }

    /**
     * Get avg_price.
     *
     * @return string
     */
    public function getAvgPrice()
    {
        return $this->avg_price;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return ItemPrice
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     *
     * @return ItemPrice
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
     * Set region_id.
     *
     * @param int $regionId
     *
     * @return ItemPrice
     */
    public function setRegionId($regionId)
    {
        $this->region_id = $regionId;

        return $this;
    }

    /**
     * Get region_id.
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

    /**
     * Set region_name.
     *
     * @param string $regionName
     *
     * @return ItemPrice
     */
    public function setRegionName($regionName)
    {
        $this->region_name = $regionName;

        return $this;
    }

    /**
     * Get region_name.
     *
     * @return string
     */
    public function getRegionName()
    {
        return $this->region_name;
    }

    /**
     * Set type_id.
     *
     * @param int $typeId
     *
     * @return ItemPrice
     */
    public function setTypeId($typeId)
    {
        $this->type_id = $typeId;

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
     * Set type_name.
     *
     * @param string $typeName
     *
     * @return ItemPrice
     */
    public function setTypeName($typeName)
    {
        $this->type_name = $typeName;

        return $this;
    }

    /**
     * Get type_name.
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->type_name;
    }
}
