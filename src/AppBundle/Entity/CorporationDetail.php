<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="corporation_details", uniqueConstraints={
     @ORM\UniqueConstraint(name="name_idx", columns={"name"})
 * })
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class CorporationDetail
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $ticker;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $ceo_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $ceo_name;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $headquarters_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $headquarters_name;

    /**
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $url;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $alliance_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $alliance_name;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=16)
     * @JMS\Expose()
     */
    protected $tax_rate;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $member_count;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $member_limit;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $shares;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="corporation_details")
     */
    protected $corporation;

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
     * Set name
     *
     * @param string $name
     * @return CorporationDetail
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
     * Set ticker
     *
     * @param string $ticker
     * @return CorporationDetail
     */
    public function setTicker($ticker)
    {
        $this->ticker = $ticker;

        return $this;
    }

    /**
     * Get ticker
     *
     * @return string 
     */
    public function getTicker()
    {
        return $this->ticker;
    }

    /**
     * Set ceo_id
     *
     * @param integer $ceoId
     * @return CorporationDetail
     */
    public function setCeoId($ceoId)
    {
        $this->ceo_id = $ceoId;

        return $this;
    }

    /**
     * Get ceo_id
     *
     * @return integer 
     */
    public function getCeoId()
    {
        return $this->ceo_id;
    }

    /**
     * Set ceo_name
     *
     * @param string $ceoName
     * @return CorporationDetail
     */
    public function setCeoName($ceoName)
    {
        $this->ceo_name = $ceoName;

        return $this;
    }

    /**
     * Get ceo_name
     *
     * @return string 
     */
    public function getCeoName()
    {
        return $this->ceo_name;
    }

    /**
     * Set headquarters_id
     *
     * @param integer $headquartersId
     * @return CorporationDetail
     */
    public function setHeadquartersId($headquartersId)
    {
        $this->headquarters_id = $headquartersId;

        return $this;
    }

    /**
     * Get headquarters_id
     *
     * @return integer 
     */
    public function getHeadquartersId()
    {
        return $this->headquarters_id;
    }

    /**
     * Set headquarters_name
     *
     * @param string $headquartersName
     * @return CorporationDetail
     */
    public function setHeadquartersName($headquartersName)
    {
        $this->headquarters_name = $headquartersName;

        return $this;
    }

    /**
     * Get headquarters_name
     *
     * @return string 
     */
    public function getHeadquartersName()
    {
        return $this->headquarters_name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return CorporationDetail
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return CorporationDetail
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alliance_id
     *
     * @param integer $allianceId
     * @return CorporationDetail
     */
    public function setAllianceId($allianceId)
    {
        $this->alliance_id = $allianceId;

        return $this;
    }

    /**
     * Get alliance_id
     *
     * @return integer 
     */
    public function getAllianceId()
    {
        return $this->alliance_id;
    }

    /**
     * Set alliance_name
     *
     * @param string $allianceName
     * @return CorporationDetail
     */
    public function setAllianceName($allianceName)
    {
        $this->alliance_name = $allianceName;

        return $this;
    }

    /**
     * Get alliance_name
     *
     * @return string 
     */
    public function getAllianceName()
    {
        return $this->alliance_name;
    }

    /**
     * Set tax_rate
     *
     * @param string $taxRate
     * @return CorporationDetail
     */
    public function setTaxRate($taxRate)
    {
        $this->tax_rate = $taxRate;

        return $this;
    }

    /**
     * Get tax_rate
     *
     * @return string 
     */
    public function getTaxRate()
    {
        return $this->tax_rate;
    }

    /**
     * Set member_count
     *
     * @param integer $memberCount
     * @return CorporationDetail
     */
    public function setMemberCount($memberCount)
    {
        $this->member_count = $memberCount;

        return $this;
    }

    /**
     * Get member_count
     *
     * @return integer 
     */
    public function getMemberCount()
    {
        return $this->member_count;
    }

    /**
     * Set member_limit
     *
     * @param integer $memberLimit
     * @return CorporationDetail
     */
    public function setMemberLimit($memberLimit)
    {
        $this->member_limit = $memberLimit;

        return $this;
    }

    /**
     * Get member_limit
     *
     * @return integer 
     */
    public function getMemberLimit()
    {
        return $this->member_limit;
    }

    /**
     * Set corporation
     *
     * @param \AppBundle\Entity\Corporation $corporation
     * @return CorporationDetail
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return CorporationDetail
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
     * Set shares
     *
     * @param integer $shares
     * @return CorporationDetail
     */
    public function setShares($shares)
    {
        $this->shares = $shares;

        return $this;
    }

    /**
     * Get shares
     *
     * @return integer 
     */
    public function getShares()
    {
        return $this->shares;
    }
}
