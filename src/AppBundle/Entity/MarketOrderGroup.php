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

}
