<?php

require(dirname(__FILE__).'/../loader.php');

$lib = array('curl');

Libs::load3rd($lib);

function api_key()
{
	/** @var Kumpulan API Key */
	$key_bank = array(array('P20APVP31JG3U0UJC4ZPWSSWW5GMP4WJ014TA5JAGWYXJBLD', 'OQIS4CBVG1TNQCRQMWOBHLOCZMCP5ZKPCF1AMXBS13EI5MEE'),
					  array('44KWZR2C3HVDWSTGPEOEHFTNA2DHL32BKDCWUJC3HD1ZDHZF', 'LXSUSXZWIMARWLTVBZT3HVUYOA5RN0SNR1NUJF4AFRQRBWQ4'),
					  array('A2BZQ3VFIILKB0KA1BMLV1DXS5M0E3BSNRW1FVDOXI20OELK', 'OWKK1WFBYICBUBL0VC5UF4UNTHAC0TPE0LY2LJW1C1EYN31I'),
					  array('AXXY1AEIL1MVUIS2JKJTSJEMBLKX0IFE223EDVQPZFBR42QB', 'U5TQYKX3F1CNOVH1PT5QCKSM4WSL2H0NEKXUGRCHTOJTYHIB'),
					  array('A4NVEI2FKX3QR5CBC24S4TIKTY1WXWJ2ZSO5VPGLMKARPM0I', 'QODM2JM2Z4BVZ5DXVI0F2U050DSNEN2B2B5LHTDTOLUD5CFX')
					);

	/** @var API Key di acak */
	return $key_bank[array_rand($key_bank)];
}

$api_key = api_key();

$api = 'https://api.foursquare.com/v2/venues/explore?venuePhotos=1&query=Hotel&ll=-6.943256,108.52196&client_id='.$api_key[0].'&client_secret='.$api_key[1].'&v=20140103';

$cURL = new cURLs(array('url'=>$api,'type'=>'data'));

$get = $cURL->access_curl();

header("Content-Type: application/json; charset=UTF-8");
echo $get;

?>