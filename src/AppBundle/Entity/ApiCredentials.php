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
     * @ORM\Column(type="text")
     */
    protected $api_key;

    /**
     * @ORM\Column(type="text")
     * @JMS\Expose()
     */
    protected $type;

    /**
     * @ORM\Column(type="text")
     */
    protected $verification_code;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Corporation", inversedBy="api_credentials")
     */
    protected $corporation;

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
}
