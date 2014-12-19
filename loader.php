<?php
require_once(dirname(__FILE__).'/config.php');

function loader($data)
{
	if(is_array($data))
	{
		foreach($data as $lib){
			$lib_data = VENDORPATH.'/'.$lib.'.php';
			if(file_exists($lib_data)) require_once($lib_data);
		}
	}
}

?>