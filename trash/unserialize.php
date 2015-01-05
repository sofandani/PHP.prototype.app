<?php

function ObjectToArray($data)
{
    $array = array();
    foreach ($data as $key => $value) {
        if ($value instanceof StdClass) {
            $array[$key] = $value->toArray();
        } else {
            $array[$key] = $value;
        }
    }
    return $array;
}

$obj = 'O:8:"stdClass":3:{s:5:"count";i:22;s:8:"has_more";b:0;s:6:"photos";a:0:{}}';

//$obj = serialize(json_decode(file_get_contents(dirname(__FILE__).'/../cache/geolocation-ec810fd6411657e2b2a4a8a5bdf1b291.json')));

$unserialize = ObjectToArray(unserialize($obj));

print_r($unserialize);

?>