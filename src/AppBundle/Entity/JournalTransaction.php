<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="journal_transactions")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class JournalTransaction {

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $refId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $refTypeId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ownerName1;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ownerId1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ownerName2;

    /**
     * @ORM\Column(type="integer")
     */
    protected  $ownerId2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $argName1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $argId1;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    protected $amount;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     */
    protected $balance;

    /**
     * @ORM\Column(type="string")
     */
    protected $reason;

    /**
     * @ORM\Column(type="string")
     */
    protected $taxReceiverId;

    /**
     * @ORM\Column(type="string")
     */
    protected $taxAmount;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account", inversedBy="journal_transactions")
     */
    protected $account;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

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
     * Set date
     *
     * @param \DateTime $date
     * @return JournalTransaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set refId
     *
     * @param integer $refId
     * @return JournalTransaction
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Get refId
     *
     * @return integer 
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set refTypeId
     *
     * @param integer $refTypeId
     * @return JournalTransaction
     */
    public function setRefTypeId($refTypeId)
    {
        $this->refTypeId = $refTypeId;

        return $this;
    }

    /**
     * Get refTypeId
     *
     * @return integer 
     */
    public function getRefTypeId()
    {
        return $this->refTypeId;
    }

    /**
     * Set ownerName1
     *
     * @param string $ownerName1
     * @return JournalTransaction
     */
    public function setOwnerName1($ownerName1)
    {
        $this->ownerName1 = $ownerName1;

        return $this;
    }

    /**
     * Get ownerName1
     *
     * @return string 
     */
    public function getOwnerName1()
    {
        return $this->ownerName1;
    }

    /**
     * Set ownerId1
     *
     * @param integer $ownerId1
     * @return JournalTransaction
     */
    public function setOwnerId1($ownerId1)
    {
        $this->ownerId1 = $ownerId1;

        return $this;
    }

    /**
     * Get ownerId1
     *
     * @return integer 
     */
    public function getOwnerId1()
    {
        return $this->ownerId1;
    }

    /**
     * Set ownerName2
     *
     * @param string $ownerName2
     * @return JournalTransaction
     */
    public function setOwnerName2($ownerName2)
    {
        $this->ownerName2 = $ownerName2;

        return $this;
    }

    /**
     * Get ownerName2
     *
     * @return string 
     */
    public function getOwnerName2()
    {
        return $this->ownerName2;
    }

    /**
     * Set ownerId2
     *
     * @param integer $ownerId2
     * @return JournalTransaction
     */
    public function setOwnerId2($ownerId2)
    {
        $this->ownerId2 = $ownerId2;

        return $this;
    }

    /**
     * Get ownerId2
     *
     * @return integer 
     */
    public function getOwnerId2()
    {
        return $this->ownerId2;
    }

    /**
     * Set argName1
     *
     * @param string $argName1
     * @return JournalTransaction
     */
    public function setArgName1($argName1)
    {
        $this->argName1 = $argName1;

        return $this;
    }

    /**
     * Get argName1
     *
     * @return string 
     */
    public function getArgName1()
    {
        return $this->argName1;
    }

    /**
     * Set argId1
     *
     * @param string $argId1
     * @return JournalTransaction
     */
    public function setArgId1($argId1)
    {
        $this->argId1 = $argId1;

        return $this;
    }

    /**
     * Get argId1
     *
     * @return string 
     */
    public function getArgId1()
    {
        return $this->argId1;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return JournalTransaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return JournalTransaction
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
     * Set reason
     *
     * @param string $reason
     * @return JournalTransaction
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set taxReceiverId
     *
     * @param string $taxReceiverId
     * @return JournalTransaction
     */
    public function setTaxReceiverId($taxReceiverId)
    {
        $this->taxReceiverId = $taxReceiverId;

        return $this;
    }

    /**
     * Get taxReceiverId
     *
     * @return string 
     */
    public function getTaxReceiverId()
    {
        return $this->taxReceiverId;
    }

    /**
     * Set taxAmount
     *
     * @param string $taxAmount
     * @return JournalTransaction
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * Get taxAmount
     *
     * @return string 
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return JournalTransaction
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
     * Set account
     *
     * @param \AppBundle\Entity\Account $account
     * @return JournalTransaction
     */
    public function setAccount(\AppBundle\Entity\Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \AppBundle\Entity\Account 
     */
    public function getAccount()
    {
        return $this->account;
    }
}
