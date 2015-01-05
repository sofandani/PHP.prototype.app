<?php

define('BASEDIR', dirname(__FILE__));

require_once(BASEDIR.'/../loader.php');

$libs = array( 'curl', 'geocode' );

Libs::load3rd($libs);

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

try
{
	$array = array('city'=>$city,'typedata'=>'json','type_save'=>'file');
	$GoogleGeocode = GoogleGeocode::get_geocode($array);
	
	//print_r( $GoogleGeocode );

	echo $GoogleGeocode->results[0]->geometry->location->lat;
}
catch(GoogleGeocodeException $e)
{
	throw new GoogleGeocodeException($e->getMessage());
}

?>