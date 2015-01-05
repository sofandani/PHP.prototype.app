<?php

define('BASEDIR', dirname(__FILE__));

require_once(BASEDIR.'/../loader.php');

$libs = array( 'curl', 'xml.converter', 'rss.xml' );

Libs::load3rd($libs);

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

try
{
	$feed = (new NewsGoogleFeed)->_RequestData($city);
	print_r(serialize($feed));
}
catch(Exception $e)
{
	echo $e->getMessage();
}

?>