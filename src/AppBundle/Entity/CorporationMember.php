<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorporationMemberRepository")
 * @ORM\Table(name="corporation_members", uniqueConstraints={
    @ORM\UniqueConstraint(name="member_corp_idx", columns={"character_id","corporation_id"})
 * })
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
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
     */
    protected $character_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $character_name;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $start_time;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="corporation_members")
     */
    protected $corporation;

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
     * Set character_id
     *
     * @param integer $characterId
     * @return CorporationMember
     */
    public function setCharacterId($characterId)
    {
        $this->character_id = $characterId;

        return $this;
    }

    /**
     * Get character_id
     *
     * @return integer 
     */
    public function getCharacterId()
    {
        return $this->character_id;
    }

    /**
     * Set character_name
     *
     * @param string $characterName
     * @return CorporationMember
     */
    public function setCharacterName($characterName)
    {
        $this->character_name = $characterName;

        return $this;
    }

    /**
     * Get character_name
     *
     * @return string 
     */
    public function getCharacterName()
    {
        return $this->character_name;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return CorporationMember
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return CorporationMember
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
     * @return CorporationMember
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
