<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Office;

class SearchController extends Controller
{
	
	private $defaults = ["_search" => "Zoek stad..."];
    
    /**
     * @Route("search/", name="search")
     */
    public function drawSearch()
    {
    	$twig = array();
    	
    	$twig["defaults"] = $this->defaults;
    	
    	return $this->render('search/search.html.twig', $twig);
    }  
    
    /**
     * @Route("search/results/", name="search:results")
     */
    public function drawSearchResults(Request $request)
    {
    	$twig = array();
    	$json = array();
    	$twig["showMore"] = false;
    	
    	$search = $request->request->get('search');
    	$search_support = $request->request->get('search_has_support_desk') == 1?true:false;
    	$search_weekends = $request->request->get('search_is_open_during_weekends') == 1?true:false;
    	$limit = $request->request->get('search_limit');
    	$offset = $request->request->get('search_offset');
    	if(is_null($limit))
    		$limit = 7;
    	
    	$data_manager = $this->getDoctrine()->getManager();
  		$twig["offices"] = $data_manager->getRepository('AppBundle:Office')->findByCity($search, $search_support, $search_weekends, $limit + 1, $offset);
  		
  		$cnt_offices = count($twig["offices"]);
  		if($cnt_offices > $limit)
  		{
    		$twig["showMore"] = true;
    		unset($twig["offices"][$cnt_offices-1]);
  		}
  			
    	$json["html"] = $this->renderView('search/results.html.twig', $twig);
    	$json["limit"] = $limit;
    	$json["offset"] = $offset;
    	
    	$json["offices"] = Array();
    	foreach($twig["offices"] as $office)
    	{
    		$json["offices"][] = ["street" => $office->getStreet(),
    							  "city" => $office->getCity()->getCityName(),
    							  "province" => $office->getCity()->getProvince()->getProvinceName(),
    							  "latitude" => $office->getLatitude(),
    							  "longitude" => $office->getLongitude(),
    							  "isOpenInWeekends" => $office->isOpenInWeekends(),
    							  "hasSupportDesk" => $office->hasSupportDesk()];
    	}

    	return new JsonResponse($json);
    }
    
    /**
     * @Route("search/suggestions/", name="search:suggestions")
     */
    public function getSearchSuggestions(Request $request)
    {
    	$data_manager = $this->getDoctrine()->getManager();
    	$cities = $data_manager->getRepository('AppBundle:City')->findByPartialCityName($request->request->get('search'));
    	
    	$suggestions = array();
    	
    	foreach($cities as $city)
    	{
    		if($city->getCityName() != $city->getProvince()->getProvinceName())
    			$value = $city->getCityName() . ", " . $city->getProvince()->getProvinceName();
    		else
    			$value = $city->getCityName();
    		$suggestions[] = ["name" => $city->getCityName(), "label" => $value];
    	}
    	
    	return new JsonResponse($suggestions);
    }
}
