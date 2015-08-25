<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="string", nullable=true)
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="accounts")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $eve_account_id;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $division;

    /**
     * @JMS\Expose()
     */
    protected $current_balance;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AccountBalance", cascade={"persist"}, mappedBy="account")
     */
    protected $balances;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MarketTransaction", cascade={"persist"}, mappedBy="account")
     */
    protected $market_transactions;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\JournalTransaction", cascade={"persist"}, mappedBy="account")
     */
    protected $journal_transactions;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function __construct(){
        $this->created_at = new \DateTime();
        $this->balances = new ArrayCollection();
        $this->market_transactions = new ArrayCollection();
        $this->journal_transactions = new ArrayCollection();
    }

    /**
     * Add balances
     *
     * @param \AppBundle\Entity\AccountBalance $balances
     * @return Account
     */
    public function addBalance(\AppBundle\Entity\AccountBalance $balances)
    {
        if (!$this->balances->contains($balances)){
            $this->balances[] = $balances;
            $balances->setAccount($this);
        }

        return $this;
    }

    /**
     * Add transactions
     *
     * @param \AppBundle\Entity\MarketTransaction $transactions
     * @return Account
     */
    public function addMarketTransaction(\AppBundle\Entity\MarketTransaction $transactions)
    {
        if (!$this->market_transactions->contains($transactions)){
            $this->market_transactions[] = $transactions;
            $transactions->setAccount($this);
        }

        return $this;
    }

    /**
     * Add journal_transactions
     *
     * @param \AppBundle\Entity\JournalTransaction $journalTransactions
     * @return Account
     */
    public function addJournalTransaction(\AppBundle\Entity\JournalTransaction $journalTransactions)
    {
        if (!$this->journal_transactions->contains($journalTransactions)){
            $this->journal_transactions[] = $journalTransactions;
            $journalTransactions->setAccount($this);

        }

        return $this;
    }

    public function getCurrentBalance(){
        return $this->current_balance;
    }

    public function setCurrentBalance($balance){
        $this->current_balance = floatval($balance);
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

    /**
     * Remove balances
     *
     * @param \AppBundle\Entity\AccountBalance $balances
     */
    public function removeBalance(\AppBundle\Entity\AccountBalance $balances)
    {
        $this->balances->removeElement($balances);
    }

    /**
     * Get balances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBalances()
    {
        return $this->balances;
    }


    /**
     * Remove transactions
     *
     * @param \AppBundle\Entity\MarketTransaction $transactions
     */
    public function removeMarketTransaction(\AppBundle\Entity\MarketTransaction $transactions)
    {
        $this->market_transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMarketTransactions()
    {
        return $this->market_transactions;
    }

    /**
     * Remove journal_transactions
     *
     * @param \AppBundle\Entity\JournalTransaction $journalTransactions
     */
    public function removeJournalTransaction(\AppBundle\Entity\JournalTransaction $journalTransactions)
    {
        $this->journal_transactions->removeElement($journalTransactions);
    }


    /**
     * Get journal_transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJournalTransactions()
    {
        return $this->journal_transactions;
    }
}
