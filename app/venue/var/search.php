<?php if ( !defined('BASEPATH')) header('Location:/404');

function var_venue_title($query)
{
	return 'Venue '.ucwords($query);
}

function var_venue_content($var)
{
	$r = '<div class="container clearfix">';
	foreach($var[1]->response->venues as $f):
		
	$categories = @$f->categories;
	
	if($categories)
	{
		$icon = $f->categories[0]->icon->prefix.$var[0];
	}
	else
	{
		$icon = 'https://ss3.4sqi.net/img/categories_v2/none_'.$var[0];
	}

	$r .= '
		<div class="media">
			<a class="pull-left" href="https://foursquare.com/v/'.$f->id.'" target="_blank">
				<img src="'.$icon.'" data-holder-rendered="true" style="width: 64px; height: 64px;" alt="64x64">
			</a>
			<div class="media-body">
				<h4 class="media-heading">'.ucwords($f->name).'</h4>
				<p>'.implode(', ',$f->location->formattedAddress).'</p>
			</div>
		</div>
	';
	endforeach;
	
	$r .= '</div>';

	return $r;
}

function var_venue_embed()
{
	return GenTag::css(
				array('bootstrap'=>array('href'=>'bootstrap.min.css'),
					  'bootstrap-theme'=>array('href'=>'bootstrap-theme.css'),
					  'bootstrap-addons'=>array('href'=>'bootstrap-addons.css')
				)
			);
}

$title = var_venue_title($query);

$embed = var_venue_embed();

$icon_suffix = 'bg_64.png';
$var = array($icon_suffix,$foursquare);

$content = var_venue_content($var);
?>