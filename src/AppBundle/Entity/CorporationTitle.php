<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorporationTitleRepository")
 * @ORM\Table(name="corporation_titles")
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class CorporationTitle
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="titles")
     */
    protected $corporation;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $eve_title_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $title_name;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $roles;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $grantable_roles;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $roles_at_hq;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $grantable_roles_at_hq;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $roles_at_other;

    /**
     * @ORM\Column(type="array")
     * @JMS\Expose()
     */
    protected $grantable_roles_at_other;

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
     * Set corporation
     *
     * @param \AppBundle\Entity\Corporation $corporation
     * @return CorporationTitle
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
     * Set eve_title_id
     *
     * @param integer $eveTitleId
     * @return CorporationTitle
     */
    public function setEveTitleId($eveTitleId)
    {
        $this->eve_title_id = $eveTitleId;

        return $this;
    }

    /**
     * Get eve_title_id
     *
     * @return integer 
     */
    public function getEveTitleId()
    {
        return $this->eve_title_id;
    }

    /**
     * Set title_name
     *
     * @param string $titleName
     * @return CorporationTitle
     */
    public function setTitleName($titleName)
    {
        $this->title_name = $titleName;

        return $this;
    }

    /**
     * Get title_name
     *
     * @return string 
     */
    public function getTitleName()
    {
        return $this->title_name;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return CorporationTitle
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set grantable_roles
     *
     * @param array $grantableRoles
     * @return CorporationTitle
     */
    public function setGrantableRoles($grantableRoles)
    {
        $this->grantable_roles = $grantableRoles;

        return $this;
    }

    /**
     * Get grantable_roles
     *
     * @return array 
     */
    public function getGrantableRoles()
    {
        return $this->grantable_roles;
    }

    /**
     * Set roles_at_hq
     *
     * @param array $rolesAtHq
     * @return CorporationTitle
     */
    public function setRolesAtHq($rolesAtHq)
    {
        $this->roles_at_hq = $rolesAtHq;

        return $this;
    }

    /**
     * Get roles_at_hq
     *
     * @return array 
     */
    public function getRolesAtHq()
    {
        return $this->roles_at_hq;
    }

    /**
     * Set grantable_roles_at_hq
     *
     * @param array $grantableRolesAtHq
     * @return CorporationTitle
     */
    public function setGrantableRolesAtHq($grantableRolesAtHq)
    {
        $this->grantable_roles_at_hq = $grantableRolesAtHq;

        return $this;
    }

    /**
     * Get grantable_roles_at_hq
     *
     * @return array 
     */
    public function getGrantableRolesAtHq()
    {
        return $this->grantable_roles_at_hq;
    }

    /**
     * Set roles_at_other
     *
     * @param array $rolesAtOther
     * @return CorporationTitle
     */
    public function setRolesAtOther($rolesAtOther)
    {
        $this->roles_at_other = $rolesAtOther;

        return $this;
    }

    /**
     * Get roles_at_other
     *
     * @return array 
     */
    public function getRolesAtOther()
    {
        return $this->roles_at_other;
    }

    /**
     * Set grantable_roles_at_other
     *
     * @param array $grantableRolesAtOther
     * @return CorporationTitle
     */
    public function setGrantableRolesAtOther($grantableRolesAtOther)
    {
        $this->grantable_roles_at_other = $grantableRolesAtOther;

        return $this;
    }

    /**
     * Get grantable_roles_at_other
     *
     * @return array 
     */
    public function getGrantableRolesAtOther()
    {
        return $this->grantable_roles_at_other;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return CorporationTitle
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
}
