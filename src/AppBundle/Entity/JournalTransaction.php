<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JournalTransactionRepository")
 * @ORM\Table(name="journal_transactions", uniqueConstraints={
    @ORM\UniqueConstraint(name="refId_date_acc_idx", columns={"ref_id", "date", "account_id", "amount"}),
 * })
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
     * @JMS\Expose()
     */
    protected $date;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $ref_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ref_type_id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\RefType")
     * @JMS\Expose()
     */
    protected $ref_type;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    protected $owner_name1;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $owner_id1;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    protected $owner_name2;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected  $owner_id2;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    protected $arg_name1;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    protected $arg_id1;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     * @JMS\Expose()
     */
    protected $amount;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=2)
     * @JMS\Expose()
     */
    protected $balance;

    /**
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    protected $reason;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $owner1_type_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $owner2_type_id;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Account", inversedBy="journal_transactions")
     * @JMS\Expose()
     */
    protected $account;

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
     * Set ref_id
     *
     * @param integer $refId
     * @return JournalTransaction
     */
    public function setRefId($refId)
    {
        $this->ref_id = intval($refId);

        return $this;
    }

    /**
     * Get ref_id
     *
     * @return integer 
     */
    public function getRefId()
    {
        return $this->ref_id;
    }

    /**
     * Set ref_type_id
     *
     * @param integer $refTypeId
     * @return JournalTransaction
     */
    public function setRefTypeId($refTypeId)
    {
        $this->ref_type_id = intval($refTypeId);

        return $this;
    }

    /**
     * Get ref_type_id
     *
     * @return integer 
     */
    public function getRefTypeId()
    {
        return $this->ref_type_id;
    }

    /**
     * Set owner_name1
     *
     * @param string $ownerName1
     * @return JournalTransaction
     */
    public function setOwnerName1($ownerName1)
    {
        $this->owner_name1 = $ownerName1;

        return $this;
    }

    /**
     * Get owner_name1
     *
     * @return string 
     */
    public function getOwnerName1()
    {
        return $this->owner_name1;
    }

    /**
     * Set owner_id1
     *
     * @param integer $ownerId1
     * @return JournalTransaction
     */
    public function setOwnerId1($ownerId1)
    {
        $this->owner_id1 = intval($ownerId1);

        return $this;
    }

    /**
     * Get owner_id1
     *
     * @return integer 
     */
    public function getOwnerId1()
    {
        return $this->owner_id1;
    }

    /**
     * Set owner_name2
     *
     * @param string $ownerName2
     * @return JournalTransaction
     */
    public function setOwnerName2($ownerName2)
    {
        $this->owner_name2 = $ownerName2;

        return $this;
    }

    /**
     * Get owner_name2
     *
     * @return string 
     */
    public function getOwnerName2()
    {
        return $this->owner_name2;
    }

    /**
     * Set owner_id2
     *
     * @param integer $ownerId2
     * @return JournalTransaction
     */
    public function setOwnerId2($ownerId2)
    {
        $this->owner_id2 = intval($ownerId2);

        return $this;
    }

    /**
     * Get owner_id2
     *
     * @return integer 
     */
    public function getOwnerId2()
    {
        return $this->owner_id2;
    }

    /**
     * Set arg_name1
     *
     * @param string $argName1
     * @return JournalTransaction
     */
    public function setArgName1($argName1)
    {
        $this->arg_name1 = $argName1;

        return $this;
    }

    /**
     * Get arg_name1
     *
     * @return string 
     */
    public function getArgName1()
    {
        return $this->arg_name1;
    }

    /**
     * Set arg_id1
     *
     * @param string $argId1
     * @return JournalTransaction
     */
    public function setArgId1($argId1)
    {
        $this->arg_id1 = intval($argId1);

        return $this;
    }

    /**
     * Get arg_id1
     *
     * @return string 
     */
    public function getArgId1()
    {
        return $this->arg_id1;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return JournalTransaction
     */
    public function setAmount($amount)
    {
        $this->amount = floatval($amount);

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
        $this->balance = floatval($balance);

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

    /**
     * Set owner1_type_id
     *
     * @param string $owner1TypeId
     * @return JournalTransaction
     */
    public function setOwner1TypeId($owner1TypeId)
    {
        $this->owner1_type_id = $owner1TypeId;

        return $this;
    }

    /**
     * Get owner1_type_id
     *
     * @return string 
     */
    public function getOwner1TypeId()
    {
        return $this->owner1_type_id;
    }

    /**
     * Set owner2_type_id
     *
     * @param string $owner2TypeId
     * @return JournalTransaction
     */
    public function setOwner2TypeId($owner2TypeId)
    {
        $this->owner2_type_id = $owner2TypeId;

        return $this;
    }

    /**
     * Get owner2_type_id
     *
     * @return string 
     */
    public function getOwner2TypeId()
    {
        return $this->owner2_type_id;
    }

    /**
     * Set ref_type
     *
     * @param \AppBundle\Entity\RefType $refType
     * @return JournalTransaction
     */
    public function setRefType(\AppBundle\Entity\RefType $refType = null)
    {
        $this->ref_type = $refType;

        return $this;
    }

    /**
     * Get ref_type
     *
     * @return \AppBundle\Entity\RefType 
     */
    public function getRefType()
    {
        return $this->ref_type;
    }
}
