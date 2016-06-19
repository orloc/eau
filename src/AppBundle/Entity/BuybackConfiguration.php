<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuybackConfigurationRepository")
 * @ORM\Table(name="buyback_configurations")
 * @JMS\ExclusionPolicy("all")
 */
class BuybackConfiguration
{
    const TYPE_GLOBAL = 1,
        TYPE_SINGLE = 2,
        TYPE_REGION = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="buyback_configurations", fetch="EAGER")
     * @JMS\Expose()
     */
    protected $corporation;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @JMS\Expose()
     */
    protected $region;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $type;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @JMS\Expose()
     */
    protected $single_item;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16, nullable=true)
     * @JMS\Expose()
     */
    protected $base_markdown;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16, nullable=true)
     * @JMS\Expose()
     */
    protected $override;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    /**
     * Constructor.
     */
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
     * Set regions.
     *
     * @param array $regions
     *
     * @return BuybackConfiguration
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get regions.
     *
     * @return array
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return BuybackConfiguration
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set override.
     *
     * @param string $override
     *
     * @return BuybackConfiguration
     */
    public function setOverride($override)
    {
        $this->override = $override;

        return $this;
    }

    /**
     * Get override.
     *
     * @return string
     */
    public function getOverride()
    {
        return $this->override;
    }

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     *
     * @return BuybackConfiguration
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
     * Set corporation.
     *
     * @param \AppBundle\Entity\Corporation $corporation
     *
     * @return BuybackConfiguration
     */
    public function setCorporation(\AppBundle\Entity\Corporation $corporation = null)
    {
        $this->corporation = $corporation;

        return $this;
    }

    /**
     * Get corporation.
     *
     * @return \AppBundle\Entity\Corporation
     */
    public function getCorporation()
    {
        return $this->corporation;
    }

    /**
     * Set single_item.
     *
     * @param int $singleItem
     *
     * @return BuybackConfiguration
     */
    public function setSingleItem($singleItem)
    {
        $this->single_item = $singleItem;

        return $this;
    }

    /**
     * Get single_item.
     *
     * @return int
     */
    public function getSingleItem()
    {
        return $this->single_item;
    }

    /**
     * Set base_markdown.
     *
     * @param string $baseMarkdown
     *
     * @return BuybackConfiguration
     */
    public function setBaseMarkdown($baseMarkdown)
    {
        $this->base_markdown = $baseMarkdown;

        return $this;
    }

    /**
     * Get base_markdown.
     *
     * @return string
     */
    public function getBaseMarkdown()
    {
        return $this->base_markdown;
    }
}
