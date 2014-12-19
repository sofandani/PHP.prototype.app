<?php
header('Content-Type: text/html; charset=utf-8');

if (!ini_get('date.timezone')) {
	date_default_timezone_set("Asia/Jakarta");
}

require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

try
{
	$rss = NewsGoogleFeed::loadRss('"'.$city.'"');

	include(dirname(__FILE__).'/template/html.php');
}
catch(NewsGoogleFeedException $e)
{
 	echo $e->getMessage();
}
?>