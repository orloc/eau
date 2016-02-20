<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConquerableStationRepository")
 * @ORM\Table(name="conquerable_stations")
 * @JMS\ExclusionPolicy("all")
 */
class ConquerableStation
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
    protected $station_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $station_name;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $station_type_id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $solar_system_id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $corporation_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $corporation_name;

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
     * Set station_id.
     *
     * @param int $stationId
     *
     * @return ConquerableStation
     */
    public function setStationId($stationId)
    {
        $this->station_id = $stationId;

        return $this;
    }

    /**
     * Get station_id.
     *
     * @return int
     */
    public function getStationId()
    {
        return $this->station_id;
    }

    /**
     * Set station_name.
     *
     * @param string $stationName
     *
     * @return ConquerableStation
     */
    public function setStationName($stationName)
    {
        $this->station_name = $stationName;

        return $this;
    }

    /**
     * Get station_name.
     *
     * @return string
     */
    public function getStationName()
    {
        return $this->station_name;
    }

    /**
     * Set station_type_id.
     *
     * @param int $stationTypeId
     *
     * @return ConquerableStation
     */
    public function setStationTypeId($stationTypeId)
    {
        $this->station_type_id = $stationTypeId;

        return $this;
    }

    /**
     * Get station_type_id.
     *
     * @return int
     */
    public function getStationTypeId()
    {
        return $this->station_type_id;
    }

    /**
     * Set solar_system_id.
     *
     * @param int $solarSystemId
     *
     * @return ConquerableStation
     */
    public function setSolarSystemId($solarSystemId)
    {
        $this->solar_system_id = $solarSystemId;

        return $this;
    }

    /**
     * Get solar_system_id.
     *
     * @return int
     */
    public function getSolarSystemId()
    {
        return $this->solar_system_id;
    }

    /**
     * Set corporation_id.
     *
     * @param int $corporationId
     *
     * @return ConquerableStation
     */
    public function setCorporationId($corporationId)
    {
        $this->corporation_id = $corporationId;

        return $this;
    }

    /**
     * Get corporation_id.
     *
     * @return int
     */
    public function getCorporationId()
    {
        return $this->corporation_id;
    }

    /**
     * Set corporation_name.
     *
     * @param string $corporationName
     *
     * @return ConquerableStation
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
}
