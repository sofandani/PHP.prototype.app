<?php if ( !defined('BASEPATH')) header('Location:/404');
function style_default_exception_handler(Exception $e){
	$title = "Sesuatu Telah Terjadi";

	$content = '<div class="container clearfix"><div class="panel panel-danger">';
	$content .= '<div class="panel-heading">Ada pesan kesalahan yang disampaikan sistem, yaitu:</div>';
	$content .= '<div class="panel-body">'.strtoupper('"'.$e->getMessage().'"').'</div>';
	$content .= '</div></div>';

	$embed = GenTag::css(array(	'bootstrap'=>array('href'=>'bootstrap.css'),
								'bootstrap-theme'=>array('href'=>'bootstrap-theme.css')
								)
						);
	include(TEMPLATEHTML);
}

function simple_default_exception_handler(Exception $e){
	echo $e->getMessage();
}

set_exception_handler("simple_default_exception_handler");
?>