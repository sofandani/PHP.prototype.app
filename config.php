<?php
header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set("Asia/Jakarta");

define('BASEPATH', dirname(__FILE__));
define('BASENAME', basename(__DIR__));
define('LIBPATH', BASEPATH.'/lib');
define('PUBLICPATH', BASEPATH.'/public');
define('TEMPLATEPATH', PUBLICPATH.'/template');
define('TEMPLATEHTML', TEMPLATEPATH.'/html.php');

$GLOBALS['METADATA'] = json_decode(@file_get_contents(BASEPATH.'/metadata.json'));

$dbconfig = $GLOBALS['METADATA']->setup->db;
define('DB_HOST', $dbconfig->server);
define('DB_USERNAME', $dbconfig->username);
define('DB_PASSWORD', $dbconfig->password);
define('DB_NAME', $dbconfig->table);

$defaultlib = $GLOBALS['METADATA']->require;
$GLOBALS['DEFAULTLIB'] = $defaultlib;

$siteset = $GLOBALS['METADATA']->setup->host;
define('PREFIXHOST', $siteset->prefix);
define('DOMAINFIX', PREFIXHOST.'/'.BASENAME);

require_once(BASEPATH.'/lib/loadlib.php');
?>