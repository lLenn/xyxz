<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OfficeRepository extends EntityRepository
{
    public function findByCity($city_name = "", $hasSupportDesk = false, $isOpenDuringWeekends = false, $limit = null, $offset = null)
    {
    	$sqlString = 'SELECT o, c, p
				     FROM AppBundle:Office o
						JOIN o.city c
						JOIN c.province p';
    	
    	$condition = '';
    	if($city_name != "")
    		$condition .= ' c.cityName LIKE :city';
    	if($hasSupportDesk)
    		$condition .= ($condition != ''?' AND':'') . ' o.hasSupportDesk = :support'; 
    	if($isOpenDuringWeekends)
    		$condition .= ($condition != ''?' AND':'') . ' o.isOpenInWeekends = :weekend';
		if($condition != '')
			$sqlString .= ' WHERE' . $condition;
    	
    	$query = $this->getEntityManager()->createQuery($sqlString);
    	if($city_name != "")
    		$query->setParameter('city', $city_name);
    	if($hasSupportDesk)
    		$query->setParameter('support', 'Y');
    	if($isOpenDuringWeekends)
    		$query->setParameter('weekend', 'Y');
    	
		if(!is_null($limit) && is_numeric($limit) && $limit > 0)
		{
			$query->setMaxResults($limit);
			if(!is_null($offset) && is_numeric($offset) && $offset > 0)
				$query->setFirstResult($offset);
		}
		
		$offices = $query->getResult();
		
		if(!count($offices) && $city_name != "" && !$hasSupportDesk && !$isOpenDuringWeekends)
		{
			$city =  $this->getEntityManager()
						  ->getRepository('AppBundle:City')
						  ->findOneByCityName($city_name);
			
			if(!is_null($city))
			{
				$query = $this->getEntityManager()->createQuery
								(
									'SELECT o, c, p, DISTANCE(:cityLat, c.cityLatitude, :cityLng, c.cityLongitude) as dis
								     FROM AppBundle:Office o
										JOIN o.city c
										JOIN c.province p
								     ORDER BY dis ASC'
								)
								->setParameter('cityLat', $city->getCityLatitude())
								->setParameter('cityLng', $city->getCityLongitude())
								->setMaxResults(3);
				
				$results = $query->getResult();
				$offices = array();
				foreach($results as $result)
					$offices[] = $result[0];
			}
		}
		
		return $offices;
    }
    
    public function findByPartialCityName($city_name)
    {
		$query = $this->getEntityManager()->createQuery
						(
						    'SELECT c, p
						     FROM AppBundle:City c
								JOIN c.province p
						     WHERE c.cityName LIKE :city
							 ORDER BY c.cityName ASC'
						)
						->setParameter('city', $city_name . "%")
						->setMaxResults(7);
		$cities = $query->getResult();
		
		return $cities;
    }
}