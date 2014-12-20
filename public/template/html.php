<?php
proto_html_compression_start();
?>
<!DOCTYPE html>
<html lang="id">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= isset($meta) ? $meta : '' ?>
		<title><?= isset($title) ? $title : '' ?></title>
		<?= isset($embed) ? $embed : '' ?>
	</head>
	<body><?= isset($content) ? $content : '' ?></body>
</html>