<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApiCredentialRepository")
 * @ORM\Table(name="api_credentials", uniqueConstraints={
@ORM\UniqueConstraint(name="key_val_corpIdx", columns={"api_key", "verification_code"}),
 * })
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
     * @JMS\Expose()
     */
    protected $api_key;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $eve_character_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $eve_corporation_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose()
     */
    protected $is_active;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $verification_code;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="api_credentials")
     */
    protected $corporation;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Character", inversedBy="api_credentials")
     */
    protected $characters;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @JMS\Expose()
     */
    protected $invalid;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
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

    public function toArray(){
        return [
            'api_key' => $this->getApiKey(),
            'verification_code' => $this->getVerificationCode(),
            'access_mask' => $this->getAccessMask(),
            'type' => $this->getType()
        ];
    }

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
        $this->characters = new ArrayCollection();
        $this->is_active = false;
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
     * Set character_id
     *
     * @param integer $characterId
     * @return ApiCredentials
     */
    public function setEveCharacterId($characterId)
    {
        $this->eve_character_id = $characterId;

        return $this;
    }

    /**
     * Get character_id
     *
     * @return integer 
     */
    public function getEveCharacterId()
    {
        return $this->eve_character_id;
    }

    /**
     * Set corporation_id
     *
     * @param integer $corporationId
     * @return ApiCredentials
     */
    public function setEveCorporationId($corporationId)
    {
        $this->eve_corporation_id = $corporationId;

        return $this;
    }

    /**
     * Get corporation_id
     *
     * @return integer 
     */
    public function getEveCorporationId()
    {
        return $this->eve_corporation_id;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return ApiCredentials
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;

        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }


    /**
     * Add characters
     *
     * @param \AppBundle\Entity\Character $characters
     * @return ApiCredentials
     */
    public function addCharacter(\AppBundle\Entity\Character $characters)
    {
        $this->characters[] = $characters;

        return $this;
    }

    /**
     * Remove characters
     *
     * @param \AppBundle\Entity\Character $characters
     */
    public function removeCharacter(\AppBundle\Entity\Character $characters)
    {
        $this->characters->removeElement($characters);
    }

    /**
     * Get characters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCharacters()
    {
        return $this->characters;
    }
}
