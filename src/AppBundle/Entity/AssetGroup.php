<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetGroupRepository")
 * @ORM\Table(name="asset_groups")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class AssetGroup
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Asset", mappedBy="asset_grouping", cascade={"persist"})
     */
    protected $assets;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="asset_groupings")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16, nullable=true)
     */
    protected $asset_sum;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $has_been_updated;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->assets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created_at = new \DateTime();
        $this->has_been_updated = false;
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
    public function addAsset(\AppBundle\Entity\Asset $assets)
    {
        if (!$this->assets->contains($assets)){
            $this->assets[] = $assets;
            $assets->setAssetGroup($this);
        }

        return $this;
    }

    /**
     * Remove assets
     *
     * @param \AppBundle\Entity\Asset $assets
     */
    public function removeAsset(\AppBundle\Entity\Asset $assets)
    {
        $this->assets->removeElement($assets);
    }

    /**
     * Get assets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return AssetGrouping
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
     * @return AssetGrouping
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

    /**
     * Set asset_sum
     *
     * @param string $assetSum
     * @return AssetGroup
     */
    public function setAssetSum($assetSum)
    {
        $this->asset_sum = $assetSum;

        return $this;
    }

    /**
     * Get asset_sum
     *
     * @return string 
     */
    public function getAssetSum()
    {
        return $this->asset_sum;
    }

    /**
     * Set has_been_updated
     *
     * @param boolean $hasBeenUpdated
     * @return AssetGroup
     */
    public function setHasBeenUpdated($hasBeenUpdated)
    {
        $this->has_been_updated = $hasBeenUpdated;

        return $this;
    }

    /**
     * Get has_been_updated
     *
     * @return boolean 
     */
    public function getHasBeenUpdated()
    {
        return $this->has_been_updated;
    }
}
