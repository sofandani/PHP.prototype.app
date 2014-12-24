<?php if ( !defined('BASEPATH')) header('Location:/404');

function convert_color_temp($temp)
{
	if(empty($temp))
	{
		return null;
	}
	else
	{
		if($temp < 11)
		{
			$r = "35BFFF";
		}
		elseif($temp > 11 AND $temp <= 31)
		{
			$r = "00BFC6";
		}
		elseif($temp > 31 AND $temp <= 61)
		{
			$r = "F58839";
		}
		elseif($temp > 61 AND $temp <= 91)
		{
			$r = "FB006A";
		}
		elseif($temp > 91)
		{
			$r = "DE0303";
		}
		else
		{
			$r = "555";
		}
		return $r;
	}
}

?>