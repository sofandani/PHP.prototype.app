<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * Converts a Array into stdObject.
 * @return array
 */
function ArrayToObject($array)
{
    if (!is_array($array)) {
        return $array;
    }
    
    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
        foreach ($array as $name=>$value) {
            $name = strtolower(trim($name));
            if (!empty($name)) {
                $object->$name = arrayToObject($value);
            }
        }
        return $object;
    }
    else {
        return FALSE;
    }
}


/**
 * Converts a stdObject into Array.
 * @return array
 */
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


/**
 * Take XML content and convert
 * if to a PHP array.
 * @param string $xml Raw XML data.
 * @param string $main_heading If there is a primary heading within the XML that you only want the array for.
 * @return array XML data in array format.
 */
function xmlToArray($xml,$main_heading = '') {
    $deXml = simplexml_load_string($xml);
    $deJson = json_encode($deXml);
    $xml_array = json_decode($deJson,TRUE);
    if (! empty($main_heading)) {
        $returned = $xml_array[$main_heading];
        return $returned;
    } else {
        return $xml_array;
    }
}
?>