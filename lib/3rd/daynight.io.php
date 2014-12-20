<?php

function is_night_day_bool($time='')
{
	$default_get_times = empty($time) ? time() : $time;

	$hourly_local = date("g",$default_get_times);

	//Set condition AM or PM times
	if( date( "A", $default_get_times ) == 'AM' )
	{
		
		if ( $hourly_local == 12 || $hourly_local > 5 )
		{ // Siang
			return 1;
		}
		elseif ( $hourly_local <= 5 )
		{ // Malam
			return 0;
		}

	}
	elseif( date( "A", $default_get_times ) == 'PM' )
	{
		
		if ( $hourly_local == 12 || $hourly_local > 5 )
		{ // Malam
			return 0;
		}
		elseif ( $hourly_local <= 5 )
		{ // Siang
			return 1;
		}

	}
	else
	{
		return 1;
	}
}

?>