<?php
require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

try
{
	$param =  array('query'=>$city,
					'type_save'=>'database',
					'serialize'=>true,
					'table_cache'=>'app_cache'
					);

	$rss = NewsGoogleFeed::loadRss($param);

	include(BASEDIR.'/var/news.php');
	include(TEMPLATEHTML);
}
catch(NewsGoogleFeedException $e)
{
 	echo $e->getMessage();
}
?>