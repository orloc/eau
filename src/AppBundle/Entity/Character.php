<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CharacterRepository")
 * @ORM\Table(name="characters", uniqueConstraints={
 @ORM\UniqueConstraint(name="unique_name", columns={"eve_id"})
 * })
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity(fields={"eve_id"})
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
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $eve_corporation_id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ApiCredentials", mappedBy="characters", cascade={"persist"})
     */
    protected $api_credentials;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $corporation_name;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose()
     */
    protected $is_main;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    /**
     * @JMS\VirtualProperty()
     */
    public function hasKey()
    {
        return $this->getApiCredentials()->count() ? true : false;
    }

    public function associatedCharacters()
    {
        $chars = $this->getUser()->getCharacters();
        $otherChars = array_filter($chars->toArray(), function ($c) {
            return $c->getId() !== $this->getId();
        });

        $return = array_map(function ($c) { return [
            'name' => $c->getName(),
            'id' => $c->getId(), ];
        }, $otherChars);

        return $return;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('api_credentials', [
            new Assert\Valid(),
        ]);
    }

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->is_main = false;
        $this->api_credentials = new ArrayCollection();
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
     * Set eve_id.
     *
     * @param int $eveId
     *
     * @return Character
     */
    public function setEveId($eveId)
    {
        $this->eve_id = $eveId;

        return $this;
    }

    /**
     * Get eve_id.
     *
     * @return int
     */
    public function getEveId()
    {
        return $this->eve_id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Character
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     *
     * @return Character
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
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Character
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add api_credentials.
     *
     * @param \AppBundle\Entity\ApiCredentials $apiCredentials
     *
     * @return Character
     */
    public function addApiCredential(\AppBundle\Entity\ApiCredentials $apiCredentials)
    {
        if (!$this->api_credentials->contains($apiCredentials)) {
            $this->api_credentials[] = $apiCredentials;
            $apiCredentials->addCharacter($this);
        }

        return $this;
    }

    /**
     * Remove api_credentials.
     *
     * @param \AppBundle\Entity\ApiCredentials $apiCredentials
     */
    public function removeApiCredential(\AppBundle\Entity\ApiCredentials $apiCredentials)
    {
        $this->api_credentials->removeElement($apiCredentials);
    }

    /**
     * Get api_credentials.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApiCredentials()
    {
        return $this->api_credentials;
    }

    /**
     * Set eve_corporation_id.
     *
     * @param int $eveCorporationId
     *
     * @return Character
     */
    public function setEveCorporationId($eveCorporationId)
    {
        $this->eve_corporation_id = $eveCorporationId;

        return $this;
    }

    /**
     * Get eve_corporation_id.
     *
     * @return int
     */
    public function getEveCorporationId()
    {
        return $this->eve_corporation_id;
    }

    /**
     * Set corporation_name.
     *
     * @param string $corporationName
     *
     * @return Character
     */
    public function setCorporationName($corporationName)
    {
        $this->corporation_name = $corporationName;

        return $this;
    }

    /**
     * Get corporation_name.
     *
     * @return string
     */
    public function getCorporationName()
    {
        return $this->corporation_name;
    }

    /**
     * Set is_main.
     *
     * @param bool $isMain
     *
     * @return Character
     */
    public function setIsMain($isMain)
    {
        $this->is_main = $isMain;

        return $this;
    }

    /**
     * Get is_main.
     *
     * @return bool
     */
    public function getIsMain()
    {
        return $this->is_main;
    }
}
