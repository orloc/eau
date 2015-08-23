<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
* @ORM\Entity
* @ORM\Table(name="market_orders")
* @JMS\ExclusionPolicy("all")
*
* @package AppBundle\Entity
*/
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="market_orders")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="integer")
     */
    protected $placed_by_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $placed_at_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

    /**
     * @ORM\Column(type="integer")
     */
    protected $total_volume;

    /**
     * @ORM\Column(type="integer")
     */
    protected $volume_remaining;

    /**
     * @ORM\Column(type="integer")
     */
    protected $range;

    /**
     * @ORM\Column(type="integer")
     */
    protected $account_key;

    /**
     * @ORM\Column(type="integer")
     */
    protected $duration;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=8)
     */
    protected $escrow;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=8)
     */
    protected $price;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $bid;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $issued;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct(){
        $this->created_at = new \DateTime();
    }

}
