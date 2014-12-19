<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Panoramio <?= ucwords($city) ?></title>
	</head>
	<body>
		<div class="container">

		<ul>
		<?php
			foreach($PanoramioImages->photos as $localImage) {
				echo '<li>';
				echo '<a href="'.$localImage->photo_url.'">';
				echo '<img src="'.preg_replace('/(\/photos\/medium)/','/photos/square',$localImage->photo_file_url).'" /></a>';
				echo '<br />'.$localImage->photo_title;
				echo '</li>';
			}
		?>
		</ul>

		</div>
	</body>
</html>