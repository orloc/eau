<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="corporations")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class Corporation
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
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    protected $api_key;

    /**
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    protected $verification_code;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $access_mask;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $created_by;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose()
     */
    protected $deleted_at;

    public static function loadValidatorMetadata(ClassMetadata $metadata){
        $metadata->addPropertyConstraints('api_key',[
            new Assert\NotBlank()
        ])
        ->addPropertyConstraints('verification_code', [
            new Assert\NotBlank()
        ])
        ->addPropertyConstraints('access_mask', [
                new Assert\NotBlank()
        ]);
    }

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
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
     * @return Corporation
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
     * Set api_key
     *
     * @param string $apiKey
     * @return Corporation
     */
    public function setApiKey($apiKey)
    {
        $this->api_key = $apiKey;

        return $this;
    }

    /**
     * Get api_key
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Set verification_code
     *
     * @param string $verificationCode
     * @return Corporation
     */
    public function setVerificationCode($verificationCode)
    {
        $this->verification_code = $verificationCode;

        return $this;
    }

    /**
     * Get verification_code
     *
     * @return string 
     */
    public function getVerificationCode()
    {
        return $this->verification_code;
    }

    /**
     * Set access_mask
     *
     * @param integer $accessMask
     * @return Corporation
     */
    public function setAccessMask($accessMask)
    {
        $this->access_mask = $accessMask;

        return $this;
    }

    /**
     * Get access_mask
     *
     * @return integer 
     */
    public function getAccessMask()
    {
        return $this->access_mask;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Corporation
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
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return Corporation
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * Set created_by
     *
     * @param \AppBundle\Entity\User $createdBy
     * @return Corporation
     */
    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \AppBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }
}
