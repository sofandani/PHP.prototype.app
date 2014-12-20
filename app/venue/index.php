<?php
date_default_timezone_set("Asia/Jakarta");

require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan, Jawa Barat');
}

$city = strtolower($city);

try
{}
catch(Exception $e)
{}
?>