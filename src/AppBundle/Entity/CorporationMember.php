<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorporationMemberRepository")
 * @ORM\Table(name="corporation_members", uniqueConstraints={
 @ORM\UniqueConstraint(name="member_corp_idx", columns={"character_id","corporation_id"})
 * })
 * @JMS\ExclusionPolicy("all")
 */
class CorporationMember
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     * @JMS\Expose()
     */
    protected $character_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $character_name;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $start_time;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="corporation_members")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose()
     */
    protected $is_registered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose()
     */
    protected $disbanded_at;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    /**
     * @JMS\Expose()
     */
    protected $api_key = null;

    /**
     * @JMS\Expose()
     */
    protected $is_main = false;

    /**
     * @JMS\Expose()
     */
    protected $associated_chars = [];

    /**
     * @JMS\Expose()
     */
    protected $mains_name;

    public function setApiKey($creds)
    {
        $this->api_key = $creds;
    }

    public function setIsMain($bool)
    {
        $this->is_main = $bool;
    }

    public function setAssociatedChars(array $chars)
    {
        $this->associated_chars = $chars;
    }

    public function getAssociatedChars()
    {
        return $this->associated_chars;
    }

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->is_registered = false;
    }

    public function getIsMain()
    {
        return $this->is_main;
    }

    public function getMainsName()
    {
        return $this->mains_name;
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
     * Set character_id.
     *
     * @param int $characterId
     *
     * @return CorporationMember
     */
    public function setCharacterId($characterId)
    {
        $this->character_id = $characterId;

        return $this;
    }

    /**
     * Get character_id.
     *
     * @return int
     */
    public function getCharacterId()
    {
        return $this->character_id;
    }

    /**
     * Set character_name.
     *
     * @param string $characterName
     *
     * @return CorporationMember
     */
    public function setCharacterName($characterName)
    {
        $this->character_name = $characterName;

        return $this;
    }

    /**
     * Get character_name.
     *
     * @return string
     */
    public function getCharacterName()
    {
        return $this->character_name;
    }

    /**
     * Set start_time.
     *
     * @param \DateTime $startTime
     *
     * @return CorporationMember
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time.
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     *
     * @return CorporationMember
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
     * @return CorporationMember
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
     * Set is_registered.
     *
     * @param bool $isRegistered
     *
     * @return CorporationMember
     */
    public function setIsRegistered($isRegistered)
    {
        $this->is_registered = $isRegistered;

        return $this;
    }

    /**
     * Get is_registered.
     *
     * @return bool
     */
    public function getIsRegistered()
    {
        return $this->is_registered;
    }

    /**
     * Set disbanded_at.
     *
     * @param bool $disbandedAt
     *
     * @return CorporationMember
     */
    public function setDisbandedAt($disbandedAt)
    {
        $this->disbanded_at = $disbandedAt;

        return $this;
    }

    /**
     * Get disbanded_at.
     *
     * @return bool
     */
    public function getDisbandedAt()
    {
        return $this->disbanded_at;
    }
}
