<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="corporations", uniqueConstraints={
    @ORM\UniqueConstraint(name="name_idx", columns={"name"}),
    @ORM\UniqueConstraint(name="eve_id_idx", columns={"eve_id"})
 * })
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Corporation
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @
     * @JMS\Expose()
     */
    protected $eve_id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Account", mappedBy="corporation", cascade={"persist"})
     */
    protected $accounts;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarketOrder", mappedBy="corporation", cascade={"persist"})
     */
    protected $market_orders;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ApiCredentials", mappedBy="corporation", cascade={"persist"})
     */
    protected $api_credentials;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose()
     */
    protected $last_updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $created_by;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted_at;

    public static function loadValidatorMetadata(ClassMetadata $metadata){
        $metadata->addPropertyConstraints('api_credentials',[
            new Assert\Valid()
        ]);
    }

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->accounts = new ArrayCollection();
    }

    /**
     * Add accounts
     *
     * @param \AppBundle\Entity\Account $accounts
     * @return Corporation
     */
    public function addAccount(\AppBundle\Entity\Account $accounts)
    {
        if (!$this->accounts->contains($accounts)){
            $this->accounts[] = $accounts;
            $accounts->setCorporation($this);
        }

        return $this;
    }

    /**
     * Add market_orders
     *
     * @param \AppBundle\Entity\MarketOrder $marketOrders
     * @return Corporation
     */
    public function addMarketOrder(\AppBundle\Entity\MarketOrder $marketOrders)
    {
        if (!$this->accounts->contains($marketOrders)){
            $this->market_orders[] = $marketOrders;
            $marketOrders->setCorporation($this);
        }

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Corporation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set api_key
     *
     * @param string $apiKey
     * @return Corporation
     */
    public function setApiKey($apiKey)
    {
        $this->api_key = $apiKey;

        return $this;
    }

    /**
     * Get api_key
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Set verification_code
     *
     * @param string $verificationCode
     * @return Corporation
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verification_code = $verificationCode;

        return $this;
    }

    /**
     * Get verification_code
     *
     * @return string 
     */
    public function getVerificationCode()
    {
        return $this->verification_code;
    }

    /**
     * Set access_mask
     *
     * @param integer $accessMask
     * @return Corporation
     */
    public function setAccessMask($accessMask)
    {
        $this->access_mask = $accessMask;

        return $this;
    }

    /**
     * Get access_mask
     *
     * @return integer 
     */
    public function getAccessMask()
    {
        return $this->access_mask;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Corporation
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
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return Corporation
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Set created_by
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return Corporation
     */
    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \AppBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set api_credentials
     *
     * @param \AppBundle\Entity\ApiCredentials $apiCredentials
     * @return Corporation
     */
    public function setApiCredentials(\AppBundle\Entity\ApiCredentials $apiCredentials = null)
    {
        $this->api_credentials = $apiCredentials;
        $apiCredentials->setCorporation($this);

        return $this;
    }

    /**
     * Get api_credentials
     *
     * @return \AppBundle\Entity\ApiCredentials 
     */
    public function getApiCredentials()
    {
        return $this->api_credentials;
    }

    /**
     * Set eve_id
     *
     * @param integer $eveId
     * @return Corporation
     */
    public function setEveId($eveId)
    {
        $this->eve_id = $eveId;

        return $this;
    }

    /**
     * Get eve_id
     *
     * @return integer 
     */
    public function getEveId()
    {
        return $this->eve_id;
    }

    /**
     * Remove accounts
     *
     * @param \AppBundle\Entity\Account $accounts
     */
    public function removeAccount(\AppBundle\Entity\Account $accounts)
    {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccounts()
    {
        return $this->accounts;
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
     * Set lasted_updated_at
     *
     * @param \DateTime $lastedUpdatedAt
     * @return Corporation
     */
    public function setLastUpdatedAt($lastedUpdatedAt)
    {
        $this->last_updated_at = $lastedUpdatedAt;

        return $this;
    }

    /**
     * Get lasted_updated_at
     *
     * @return \DateTime 
     */
    public function getLastUpdatedAt()
    {
        return $this->last_updated_at;
    }
}
