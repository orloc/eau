<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="assets")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Asset
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $itemId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $locationId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $typeId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $quantity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $flag;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $singleton;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $rawQuantity;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AssetGrouping", inversedBy="assets")
     */
    protected $asset_grouping;

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
     * Set itemId
     *
     * @param integer $itemId
     * @return Asset
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer 
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set locationId
     *
     * @param integer $locationId
     * @return Asset
     */
    public function setLocationId($locationId)
    {
        $this->locationId = $locationId;

        return $this;
    }

    /**
     * Get locationId
     *
     * @return integer 
     */
    public function getLocationId()
    {
        return $this->locationId;
    }

    /**
     * Set typeId
     *
     * @param integer $typeId
     * @return Asset
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Asset
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set flag
     *
     * @param integer $flag
     * @return Asset
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return integer 
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set singleton
     *
     * @param integer $singleton
     * @return Asset
     */
    public function setSingleton($singleton)
    {
        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Get singleton
     *
     * @return integer 
     */
    public function getSingleton()
    {
        return $this->singleton;
    }

    /**
     * Set rawQuantity
     *
     * @param integer $rawQuantity
     * @return Asset
     */
    public function setRawQuantity($rawQuantity)
    {
        $this->rawQuantity = $rawQuantity;

        return $this;
    }

    /**
     * Get rawQuantity
     *
     * @return integer 
     */
    public function getRawQuantity()
    {
        return $this->rawQuantity;
    }

    /**
     * Set asset_grouping
     *
     * @param \AppBundle\Entity\AssetGrouping $assetGrouping
     * @return Asset
     */
    public function setAssetGrouping(\AppBundle\Entity\AssetGrouping $assetGrouping = null)
    {
        $this->asset_grouping = $assetGrouping;

        return $this;
    }

    /**
     * Get asset_grouping
     *
     * @return \AppBundle\Entity\AssetGrouping 
     */
    public function getAssetGrouping()
    {
        return $this->asset_grouping;
    }
}
