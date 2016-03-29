<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfficeRepository")
 * @ORM\Table(name="office")
 */
class Office
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $street;
	
	 /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="offices")
     * @ORM\JoinColumn(name="city", referencedColumnName="id")
     */
    private $city;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $latitude;
	
	/**
	 * @ORM\Column(type="float")
	 */
	private $longitude;
	
	/**
	 * @ORM\Column(name="is_open_in_weekends", type="string", length=1)
	 */
	private $isOpenInWeekends;
	
	/**
	 * @ORM\Column(name="has_support_desk", type="string", length=1)
	 */
	private $hasSupportDesk;

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
     * Set street
     *
     * @param string $street
     * @return Office
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return mb_convert_case($this->street, MB_CASE_TITLE, "UTF-8");
    }
    
    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Office
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Office
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    /**
     * Get isOpenInWeekends
     *
     * @return string 
     */
    public function isOpenInWeekends($isOpenInWeekends = null)
    {
    	if(!is_null($isOpenInWeekends))
        	$this->isOpenInWeekends = $isOpenInWeekends;
        
        return $this->getIsOpenInWeekends();
    }
    /**
     * Get hasSupportDesk
     *
     * @return string 
     */
    public function hasSupportDesk($hasSupportDesk = null)
    {
    	if(!is_null($hasSupportDesk))
        	$this->hasSupportDesk = $hasSupportDesks;
    	
        return $this->getHasSupportDesk();
    }

    /**
     * Set isOpenInWeekends
     *
     * @param string $isOpenInWeekends
     * @return Office
     */
    public function setIsOpenInWeekends($isOpenInWeekends)
    {
        $this->isOpenInWeekends = $isOpenInWeekends;

        return $this;
    }

    /**
     * Get isOpenInWeekends
     *
     * @return string 
     */
    public function getIsOpenInWeekends()
    {
        return $this->isOpenInWeekends===true?true:$this->isOpenInWeekends=="Y"?true:false;
    }

    /**
     * Set hasSupportDesk
     *
     * @param string $hasSupportDesk
     * @return Office
     */
    public function setHasSupportDesk($hasSupportDesk)
    {
        $this->hasSupportDesk = $hasSupportDesk;

        return $this;
    }

    /**
     * Get hasSupportDesk
     *
     * @return string 
     */
    public function getHasSupportDesk()
    {
        return $this->hasSupportDesk===true?true:$this->hasSupportDesk=="Y"?true:false;
    }

    /**
     * Set city
     *
     * @param \AppBundle\Entity\City $city
     * @return Office
     */
    public function setCity(\AppBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \AppBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}
