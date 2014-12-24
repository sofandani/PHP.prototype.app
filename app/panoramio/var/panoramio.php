<?php if ( !defined('BASEPATH')) header('Location:/404');
function var_panoramio_title($city)
{
	return 'Panoramio '.ucwords($city);
}

function var_panoramio_content($var)
{
	$r = '<ul>';
	foreach($var as $localImage) {
		$r .= '<li>';
		$r .= '<a href="'.$localImage->photo_url.'">';
		$r .= '<img src="'.preg_replace('/(\/photos\/medium)/','/photos/square',$localImage->photo_file_url).'" /></a>';
		$r .= '<br />'.$localImage->photo_title;
		$r .= '</li>';
	}
	$r .= '</ul>';
	return $r;
}

$title = var_panoramio_title($city);

$var = $PanoramioImages->photos;

$content = var_panoramio_content($var);

include(TEMPLATEHTML);
?>