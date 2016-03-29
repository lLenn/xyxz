<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfficeRepository")
 */
class City
{
    
    /**
     * @ORM\OneToMany(targetEntity="Office", mappedBy="city")
     */
    private $offices;
    
    public function __construct()
    {
    	$this->offices = new ArrayCollection();
    }
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="city_code", type="integer")
     */
    private $cityCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city_name", type="string", length=255)
     */
    private $cityName;

    /**
     * @var float
     *
     * @ORM\Column(name="city_latitude", type="float")
     */
    private $cityLatitude;

    /**
     * @var float
     *
     * @ORM\Column(name="city_longitude", type="float")
     */
    private $cityLongitude;


    /**
     * @ORM\ManyToOne(targetEntity="Province", inversedBy="cities")
     * @ORM\JoinColumn(name="province_id", referencedColumnName="id")
     */
    private $province;
    
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
     * Set cityCode
     *
     * @param integer $cityCode
     * @return City
     */
    public function setCityCode($cityCode)
    {
        $this->cityCode = $cityCode;

        return $this;
    }

    /**
     * Get cityCode
     *
     * @return integer 
     */
    public function getCityCode()
    {
        return $this->cityCode;
    }

    /**
     * Set cityName
     *
     * @param string $cityName
     * @return City
     */
    public function setCityName($cityName)
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * Get cityName
     *
     * @return string 
     */
    public function getCityName()
    {
        return mb_convert_case($this->cityName, MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Set cityLatitude
     *
     * @param float $cityLatitude
     * @return City
     */
    public function setCityLatitude($cityLatitude)
    {
        $this->cityLatitude = $cityLatitude;

        return $this;
    }

    /**
     * Get cityLatitude
     *
     * @return float 
     */
    public function getCityLatitude()
    {
        return $this->cityLatitude;
    }

    /**
     * Set cityLongitude
     *
     * @param float $cityLongitude
     * @return City
     */
    public function setCityLongitude($cityLongitude)
    {
        $this->cityLongitude = $cityLongitude;

        return $this;
    }

    /**
     * Get cityLongitude
     *
     * @return float 
     */
    public function getCityLongitude()
    {
        return $this->cityLongitude;
    }

    /**
     * Add offices
     *
     * @param \AppBundle\Entity\Office $offices
     * @return City
     */
    public function addOffice(\AppBundle\Entity\Office $offices)
    {
        $this->offices[] = $offices;

        return $this;
    }

    /**
     * Remove offices
     *
     * @param \AppBundle\Entity\Office $offices
     */
    public function removeOffice(\AppBundle\Entity\Office $offices)
    {
        $this->offices->removeElement($offices);
    }

    /**
     * Get offices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffices()
    {
        return $this->offices;
    }

    /**
     * Set province
     *
     * @param \AppBundle\Entity\Province $province
     * @return City
     */
    public function setProvince(\AppBundle\Entity\Province $province = null)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return \AppBundle\Entity\Province 
     */
    public function getProvince()
    {
        return $this->province;
    }
}
