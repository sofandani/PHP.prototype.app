<?php if( !defined('BASEPATH')) header('Location:/404');

function is_night_day_bool()
{
	$time = date("h A");
	$t = explode(" ", $time);
	$hours = $t[0];
	$ampm = $t[1];

	if($ampm == 'PM')
	{
		if(in_array($hours, range(01,06)))
		{
		    return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		if(in_array($hours, range(01,06)))
		{
			return 0;
		}
		else
		{
			return 1;
		}		
	}
}

?>