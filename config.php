<?php
/**
 * Test Commit
 */

header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set("Asia/Jakarta");

define('BASEPATH', dirname(__FILE__));
define('BASENAME', basename(__DIR__));
define('LIBPATH', BASEPATH.'/lib');
define('PUBLICPATH', BASEPATH.'/public');
define('TEMPLATEPATH', PUBLICPATH.'/template');
define('TEMPLATEHTML', TEMPLATEPATH.'/html.php');

$metadata = BASEPATH.'/metadata.json';

if(file_exists($metadata))
{
	$GLOBALS['METADATA'] = json_decode(@file_get_contents($metadata));
}

if(isset($GLOBALS['METADATA']))
{
	$defaultlib = $GLOBALS['METADATA']->require;
	$GLOBALS['DEFAULTLIB'] = $defaultlib;

	$siteset = $GLOBALS['METADATA']->setup->host;
	define('PREFIXHOST', $siteset->prefix);
	define('DOMAINFIX', PREFIXHOST.'/'.BASENAME);

	require_once(BASEPATH.'/lib/loadlib.php');
}
else
{
	exit('Metadata invalid.');
}
?>