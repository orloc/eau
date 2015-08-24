<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="api_credentials")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class ApiCredentials {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $api_key;

    /**
     * @ORM\Column(type="integer")
     */
    protected $character_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $corporation_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $verification_code;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="api_credentials")
     */
    protected $corporation;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="api_credentials")
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $invalid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $access_mask;

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

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @JMS\Expose()
     */
    protected $created_by;

    public static function loadValidatorMetadata(ClassMetadata $metadata){
        $metadata->addPropertyConstraints('api_key',[
            new Assert\NotBlank()
        ])
            ->addPropertyConstraints('verification_code', [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 64, 'max' => 64])
            ]);
    }

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
     * Set api_key
     *
     * @param string $apiKey
     * @return ApiCredentials
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
     * Set type
     *
     * @param string $type
     * @return ApiCredentials
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set verification_code
     *
     * @param string $verificationCode
     * @return ApiCredentials
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
     * Set invalid
     *
     * @param boolean $invalid
     * @return ApiCredentials
     */
    public function setInvalid($invalid)
    {
        $this->invalid = $invalid;

        return $this;
    }

    /**
     * Get invalid
     *
     * @return boolean 
     */
    public function getInvalid()
    {
        return $this->invalid;
    }

    /**
     * Set access_mask
     *
     * @param integer $accessMask
     * @return ApiCredentials
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
     * @return ApiCredentials
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
     * Set created_by
     *
     * @param \DateTime $createdBy
     * @return ApiCredentials
     */
    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \DateTime 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set corporation
     *
     * @param \AppBundle\Entity\Corporation $corporation
     * @return ApiCredentials
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
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return ApiCredentials
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return ApiCredentials
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

    /**
     * Set character_id
     *
     * @param integer $characterId
     * @return ApiCredentials
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
     * Set corporation_id
     *
     * @param integer $corporationId
     * @return ApiCredentials
     */
    public function setCorporationId($corporationId)
    {
        $this->corporation_id = $corporationId;

        return $this;
    }

    /**
     * Get corporation_id
     *
     * @return integer 
     */
    public function getCorporationId()
    {
        return $this->corporation_id;
    }
}
