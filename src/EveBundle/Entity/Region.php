<?php

namespace EveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="regions")
 * @package AppBundle\Entity
 */
class Region
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $region_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $region_url;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;


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
     * Set region_id
     *
     * @param integer $regionId
     * @return Region
     */
    public function setRegionId($regionId)
    {
        $this->region_id = $regionId;

        return $this;
    }

    /**
     * Get region_id
     *
     * @return integer 
     */
    public function getRegionId()
    {
        return $this->region_id;
    }

    /**
     * Set region_url
     *
     * @param string $regionUrl
     * @return Region
     */
    public function setRegionUrl($regionUrl)
    {
        $this->region_url = $regionUrl;

        return $this;
    }

    /**
     * Get region_url
     *
     * @return string 
     */
    public function getRegionUrl()
    {
        return $this->region_url;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
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
}
