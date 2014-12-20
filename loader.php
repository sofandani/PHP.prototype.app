<?php
require_once(dirname(__FILE__).'/config.php');

loadlib($GLOBALS['DEFAULTLIB'],'sys');

function loader_3rd($data)
{
	return loadlib($data,'3rd');
}

function loadlib($data,$vendor='sys')
{
	if(is_array($data))
	{
		foreach($data as $lib){
			$lib_data = LIBPATH.'/'.$vendor.'/'.$lib.'.php';
			if(file_exists($lib_data)) require_once($lib_data);
		}
	}
}
?>