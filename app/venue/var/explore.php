<?php if ( !defined('BASEPATH')) header('Location:/404');

function var_venue_title($query)
{
	return 'Venue '.ucwords( $query );
}

function var_venue_content($var)
{
	$r = '<div class="container clearfix">';

	foreach($var[1]->response->groups[0]->items as $f):
	
	$f = $f->venue;

	$categories = @$f->categories;
	
	if($categories)
	{
		$icon = $f->categories[0]->icon->prefix.$var[0];
	}
	else
	{
		$icon = 'https://ss3.4sqi.net/img/categories_v2/none_'.$var[0];
	}

	$rating = @$f->rating ? '<p>Rating: '.$f->rating.'</p>' : '';

	$photos = @$f->photos;

	$r .= '
		<div class="media">';

			if($photos)
			{
				$p = $photos->groups;
				$p = count($p) > 0 ? $p[0]->items[0] : null;
				if($p != null)
				{
					$r .= '<a class="pull-left" href="https://foursquare.com/v/'.$f->id.'" target="_blank">
					<img src="'.$p->prefix.'100'.$p->suffix.'" data-holder-rendered="true" style="width: 100px; height: 100px;" alt="100x100">
					</a>';
				}
			}

	$r .= '<div class="media-body">
			<h4 class="media-heading">
			<img src="'.$icon.'" /> '.ucwords($f->name).'</h4>
			'.$rating.'
			<p>'.implode(', ',$f->location->formattedAddress).'</p></div>
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

$icon_suffix = 'bg_32.png';
$var = array($icon_suffix,$foursquare);

$content = var_venue_content($var);
?>