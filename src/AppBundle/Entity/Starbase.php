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
}
