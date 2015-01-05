<?php
require_once(dirname(__FILE__).'/../lib/sys/xml.array.php');

$get = file_get_contents('http://news.google.com/news/section?q=Kuningan%20Jabar&output=rss');

$xml = serialize(XML2Array::createArray($get));

print_r($xml);
?>