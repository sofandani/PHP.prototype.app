<?php

require_once(dirname(__FILE__).'/../loader.php');

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Tokyo, Japan');
}

try
{
	$GoogleGeocode = new GoogleGeocode(array('city'=>$city,'typedata'=>'json'));
	$get_geocode = $GoogleGeocode->get_geocode();

	$this->_requiredLatitude = $get_geocode->results[0]->geometry->location->lat;
	$this->_requiredLongitude = $get_geocode->results[0]->geometry->location->lng;
}
catch(GoogleGeocodeException $e)
{
	throw new PanoramioException($e->getMessage());
}

?>