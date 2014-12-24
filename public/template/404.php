<?php if ( !defined('BASEPATH')) header('Location:/404');
$title = 'ERROR';
$content = 'Not Found';
$embed = GenTag::css(array('style.css'));
include(dirname(__FILE__).'/html.php');
?>