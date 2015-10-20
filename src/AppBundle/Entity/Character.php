<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="characters")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Character
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="characters")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $eve_id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ApiCredentials", mappedBy="character")
     */
    protected $api_credentials;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    public function __construct(){
        $this->created_at = new \DateTime();
        $this->api_credentials = new ArrayCollection();
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
     * Set eve_id
     *
     * @param integer $eveId
     * @return Character
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
     * Set name
     *
     * @param string $name
     * @return Character
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Character
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Character
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
