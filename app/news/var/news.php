<?php if ( !defined('BASEPATH')) header('Location:/404');
function var_news_title($city)
{
	return 'News '.ucwords($city);
}

function var_news_content($rss)
{
	$r = '<ul>';
		foreach ($rss->item as $item):
			$r .= '<li>';
				if (isset($item->{'content:encoded'})):
				$r .= '<h2>
						<a href="'.(htmlSpecialChars($item->link)).'" target="_blank">
							'.(htmlSpecialChars($item->title)).'
						</a>
						<small>'.(date("j F, Y", (int) $item->timestamp)).'</small>
					</h2>
					<div>'.($item->{'content:encoded'}).'</div>';
				else:
				$r .= '<div>'.(html_entity_decode($item->description)).'</div>';
				endif;
			$r .= '</li>';
		endforeach;
	$r .= '</ul>';
	return $r;
}

$title = var_news_title($city);

$content = var_news_content($rss);
?>