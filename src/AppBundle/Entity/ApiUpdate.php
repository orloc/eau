<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiUpdateRepository")
 * @ORM\Table(name="api_updates")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class ApiUpdate
{

    const CACHE_STYLE_SHORT = 1,
          CACHE_STYLE_LONG = 2;

    const CORP_ACC_BALANCES = 1,
          CORP_ASSET_LIST = 2,
          CORP_CONTACT_LIST = 3,
          CORP_CONTAINER_LOG = 4,
          CORP_CONTRACTS = 5,
          CORP_MARKET_ORDERS = 6,
          CORP_WALLET_JOURNAL = 7,
          CORP_WALLET_TRANSACTION = 8;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     */
    protected $api_call;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="api_updates")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose()
     */
    protected $succeeded;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
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
     * Set type
     *
     * @param integer $type
     * @return ApiUpdate
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return ApiUpdate
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
     * @return ApiUpdate
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
     * Set api_call
     *
     * @param integer $apiCall
     * @return ApiUpdate
     */
    public function setApiCall($apiCall)
    {
        $this->api_call = $apiCall;

        return $this;
    }

    /**
     * Get api_call
     *
     * @return integer 
     */
    public function getApiCall()
    {
        return $this->api_call;
    }

    /**
     * Set succeeded
     *
     * @param boolean $succeeded
     * @return ApiUpdate
     */
    public function setSucceeded($succeeded)
    {
        $this->succeeded = $succeeded;

        return $this;
    }

    /**
     * Get succeeded
     *
     * @return boolean 
     */
    public function getSucceeded()
    {
        return $this->succeeded;
    }
}
