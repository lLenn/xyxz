<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Province
 *
 * @ORM\Table(name="province")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfficeRepository")
 */
class Province
{
    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="province")
     */
    private $cities;
    
    public function __construct()
    {
    	$this->cities = new ArrayCollection();
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
     * @var string
     *
     * @ORM\Column(name="province_name", type="string", length=255)
     */
    private $provinceName;


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
     * Set provinceName
     *
     * @param string $provinceName
     * @return Province
     */
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;

        return $this;
    }

    /**
     * Get provinceName
     *
     * @return string 
     */
    public function getProvinceName()
    {
        return mb_convert_case($this->provinceName, MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Add cities
     *
     * @param \AppBundle\Entity\City $cities
     * @return Province
     */
    public function addCity(\AppBundle\Entity\City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \AppBundle\Entity\City $cities
     */
    public function removeCity(\AppBundle\Entity\City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }
}
