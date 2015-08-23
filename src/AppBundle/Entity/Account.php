<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Account
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="accounts")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="integer")
     */
    protected $eve_account_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $division;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $balance;

    /**
     * @ORM\Column(type="datetime")
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
     * Set name
     *
     * @param string $name
     * @return Account
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
     * Set eve_account_id
     *
     * @param integer $eveAccountId
     * @return Account
     */
    public function setEveAccountId($eveAccountId)
    {
        $this->eve_account_id = $eveAccountId;

        return $this;
    }

    /**
     * Get eve_account_id
     *
     * @return integer 
     */
    public function getEveAccountId()
    {
        return $this->eve_account_id;
    }

    /**
     * Set division
     *
     * @param integer $division
     * @return Account
     */
    public function setDivision($division)
    {
        $this->division = $division;

        return $this;
    }

    /**
     * Get division
     *
     * @return integer 
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return Account
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return string 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Account
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
     * @return Account
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
