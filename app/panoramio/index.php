<?php
require(dirname(__FILE__).'/lib.php');

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

try
{
	$ApiParm = array('img_num'=>100,
					 'calc_box'=>true,
					 'start_img'=>0,
					 'city'=>$city,
					 'typedata'=>'json'
					 );

	//$panoramioClass = new panoramioAPI();
	$PanoramioImages = panoramioAPI::getPanoramioImages($ApiParm);

	include(BASEDIR.'/var/panoramio.php');
}
catch(PanoramioException $e)
{
	echo $e->getMessage();
}

?>