<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuybackConfigurationRepository")
 * @ORM\Table(name="buyback_configurations")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class BuybackConfiguration
{

    const TYPE_GLOBAL = 1,
        TYPE_SINGLE = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="array")
     */
    protected $regions;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     */
    protected $override;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set regions
     *
     * @param array $regions
     * @return BuybackConfiguration
     */
    public function setRegions($regions)
    {
        $this->regions = $regions;

        return $this;
    }

    /**
     * Get regions
     *
     * @return array 
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return BuybackConfiguration
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set override
     *
     * @param string $override
     * @return BuybackConfiguration
     */
    public function setOverride($override)
    {
        $this->override = $override;

        return $this;
    }

    /**
     * Get override
     *
     * @return string 
     */
    public function getOverride()
    {
        return $this->override;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return BuybackConfiguration
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
     * @return BuybackConfiguration
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
