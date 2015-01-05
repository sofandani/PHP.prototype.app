<?php
require_once dirname(__FILE__).'/lib.php';

$query = isset($_GET['query']) ? $_GET['query'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

if($query == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?query=Kuningan, Jawa Barat&type=search');
}

$query = strtolower($query);
$type = strtolower($type);

try
{
	$args = array('type_save'=>'file',
				  'endpoint_type'=>$type,
				  'query'=>$query,
				  'expire_cache'=>strtotime('+1 Week')
				  );

	$foursquare = Foursquare::retrive_api($args);

	$var_theme = BASEDIR.'/var/'.$type.'.php';

	if(file_exists($var_theme))
	{
		include($var_theme);
		include(TEMPLATEHTML);
	}
}
catch(FoursquareException $e)
{
	echo $e->getMessage();
}
?>