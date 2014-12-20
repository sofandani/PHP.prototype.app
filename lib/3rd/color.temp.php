<?php if ( !defined('BASEPATH')) header('Location:404');

function convert_color_temp($temp)
{
	if(empty($temp))
	{
		return null;
	}
	else
	{
		if($temp < 18)
		{
			$r = "35BFFF";
		}
		elseif($temp > 18 AND $temp <= 32)
		{
			$r = "00BFC6";
		}
		elseif($temp > 32 AND $temp <= 64)
		{
			$r = "F58839";
		}
		elseif($temp > 64)
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