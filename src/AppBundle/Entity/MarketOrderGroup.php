<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MarketOrderGroupRepository")
 * @ORM\Table(name="market_order_groups")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class MarketOrderGroup
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarketOrder", mappedBy="order_grouping", cascade={"persist"})
     */
    protected $market_orders;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="asset_groupings")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->market_orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add assets
     *
     * @param \AppBundle\Entity\Asset $assets
     * @return AssetGrouping
     */
    public function addMarketOrder(\AppBundle\Entity\MarketOrder $order)
    {
        if (!$this->market_orders->contains($order)){
            $this->market_orders[] = $order;
            $order->setMarketOrderGroup($this);
        }

        return $this;
    }


    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MarketOrderGroup
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
     * Remove market_orders
     *
     * @param \AppBundle\Entity\MarketOrder $marketOrders
     */
    public function removeMarketOrder(\AppBundle\Entity\MarketOrder $marketOrders)
    {
        $this->market_orders->removeElement($marketOrders);
    }

    /**
     * Get market_orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMarketOrders()
    {
        return $this->market_orders;
    }

    /**
     * Set corporation
     *
     * @param \AppBundle\Entity\Corporation $corporation
     * @return MarketOrderGroup
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
