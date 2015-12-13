<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RefTypeRepository")
 * @ORM\Table(name="ref_types", uniqueConstraints={
    @ORM\UniqueConstraint(name="refIDidx", columns={"ref_type_id"}),
 * })
 * @JMS\ExclusionPolicy("all")
 *
 * @package AppBundle\Entity
 */
class RefType
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected $ref_type_id;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose()
     */
    protected $ref_type_name;


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
     * Set ref_type_id
     *
     * @param integer $refTypeId
     * @return RefType
     */
    public function setRefTypeId($refTypeId)
    {
        $this->ref_type_id = $refTypeId;

        return $this;
    }

    /**
     * Get ref_type_id
     *
     * @return integer 
     */
    public function getRefTypeId()
    {
        return $this->ref_type_id;
    }

    /**
     * Set ref_type_name
     *
     * @param string $refTypeName
     * @return RefType
     */
    public function setRefTypeName($refTypeName)
    {
        $this->ref_type_name = $refTypeName;

        return $this;
    }

    /**
     * Get ref_type_name
     *
     * @return string 
     */
    public function getRefTypeName()
    {
        return $this->ref_type_name;
    }
}
