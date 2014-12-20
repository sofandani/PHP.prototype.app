<?php
function var_weather_title($city)
{
	return 'Wunderground '.ucwords($city);
}

function var_weather_content($var)
{
	$r = '
		<div class="container">
			<div class="icon-weather">
				<div class="heading-weather">
					<span class="forecast-city">'.$var[0].'</span>
					<span class="forecast-name">'.$var[1].'</span>
				</div>
				<div class="body-weather" style="color:#'.convert_color_temp($var[2]).'">
					<span class="forecast-icon wi wi-'.$var[3] . $var[4].'"></span>
					<span class="forecast-temp">'.$var[5].'<i class="wi wi-celsius"></i></span>
				</div>
			</div>
		</div>
	';
	return $r;
}

function var_weather_embed()
{
	$r = '
	<link media="all" rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/weather-icons.css">
	';
	return $r;
}

$title = var_weather_title($city);

$embed = var_weather_embed();

$var = array($api_forecast_name,
			 $api_forecast_city,
			 $api_forecast_temp,
			 $suffix_icon_weather,
			 $api_forecast_icon,
			 $api_forecast_temp
			);

$content = var_weather_content($var);
?>