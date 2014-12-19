<?php
/**
 * Converts a Array into stdObject.
 * @return array
 */
function ArrayToObject($array) {
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
 * Converts a SimpleXMLElement into an array.
 * @return array
 */
function XMLtoArray(SimpleXMLElement $xml = NULL)
{
    if($xml === NULL)
    {
        $xml = $this->xml;
    }

    if(!$xml->children())
    {
        return (string) $xml;
    }

    $arr = array();
    foreach ($xml->children() as $tag => $child)
    {
        if(count($xml->$tag) === 1)
        {
            $arr[$tag] = $this->toArray($child);
        }
        else
        {
            $arr[$tag][] = $this->toArray($child);
        }
    }

    return $arr;
}
?>