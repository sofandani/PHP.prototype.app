<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Wunderground <?= ucwords($city) ?></title>
		<link media="all" rel="stylesheet" href="assets/css/style.css">
		<link rel="stylesheet" href="assets/css/weather-icons.css">
	</head>
	<body>
		<div class="container">
			<div class="icon-weather">
				<div class="heading-weather">
					<span class="forecast-city"><?= $api_forecast_city ?></span>
					<span class="forecast-name"><?= $api_forecast_name ?></span>
				</div>
				<div class="body-weather" style="color:#<?= convert_color_temp($api_forecast_temp) ?>">
					<span class="forecast-icon wi wi-<?= $suffix_icon_weather . $api_forecast_icon ?>"></span>
					<span class="forecast-temp"><?= $api_forecast_temp ?><i class="wi wi-celsius"></i></span>
				</div>
			</div>
		</div>
	</body>
</html>