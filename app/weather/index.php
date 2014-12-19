<?php
date_default_timezone_set("Asia/Jakarta");

require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

$city = strtolower($city);

try
{
	$data = array(	//'key'=>'d4c777b679398c1f',
					'lang'=>'ID',
					'city'=>$city,
					//'forecast'=>true,
					'expire_cache'=>strtotime('+30 Minute')
					);
	
	$WuForecast = new WuForecast($data);
	$decode = $WuForecast->retrive_api();

	/*
	$serialize = serialize($decode);
	$unserialize = unserialize($serialize);
	*/

	$api_forecast_icon = $decode->current_observation->icon;
	$api_forecast_name = $decode->current_observation->weather;
	$api_forecast_temp = intval($decode->current_observation->temp_c);
	$api_forecast_city = preg_replace('/((K|k)ota|(C|c)ity| )/','',$decode->current_observation->display_location->city);
	$api_forecast_time = $decode->current_observation->local_epoch;

	$suffix_icon_weather = is_night_day_bool() == 0 ? 'nt_' : '';

	include(BASEDIR.'/template/html.php');
}
catch(WuForecastException $e)
{
 	echo $e->getMessage();
}

?>