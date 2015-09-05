<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="average_prices")
 * @package AppBundle\Entity
 */
class AveragePrice
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $adjusted_price;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $average_price;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type_id;

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
     * Set adjusted_price
     *
     * @param string $adjustedPrice
     * @return AveragePrice
     */
    public function setAdjustedPrice($adjustedPrice)
    {
        $this->adjusted_price = $adjustedPrice;

        return $this;
    }

    /**
     * Get adjusted_price
     *
     * @return string 
     */
    public function getAdjustedPrice()
    {
        return $this->adjusted_price;
    }

    /**
     * Set average_price
     *
     * @param string $averagePrice
     * @return AveragePrice
     */
    public function setAveragePrice($averagePrice)
    {
        $this->average_price = $averagePrice;

        return $this;
    }

    /**
     * Get average_price
     *
     * @return string 
     */
    public function getAveragePrice()
    {
        return $this->average_price;
    }

    /**
     * Set type_id
     *
     * @param integer $typeId
     * @return AveragePrice
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return AveragePrice
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
