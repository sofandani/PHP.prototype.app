<?php
require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

$city = strtolower($city);

try
{
	$data = array(	'lang'=>'ID',
					'city'=>$city,
					'expire_cache'=>strtotime('+30 Minute'),
					'type_save'=>'database',
					//'forecast'=>true,
					//'key'=>'d4c777b679398c1f',
					);
	
	$decode = WuForecast::retrive_api($data);

	$api_forecast_icon = $decode->current_observation->icon;
	$api_forecast_name = $decode->current_observation->weather;
	$api_forecast_temp = intval($decode->current_observation->temp_c);
	$api_forecast_city = preg_replace('/((K|k)ota|(C|c)ity| )/','',$decode->current_observation->display_location->city);
	$api_forecast_time = $decode->current_observation->local_epoch;

	$suffix_icon_weather = is_night_day_bool() == 0 ? 'nt_' : '';

	include(BASEDIR.'/var/weather.php');
	include(TEMPLATEHTML);
}
catch(WuForecastException $e)
{
 	echo $e->getMessage();
}

?>