<?php

/** @var Kumpulan API Key */
$_key_bank = array( array('P20APVP31JG3U0UJC4ZPWSSWW5GMP4WJ014TA5JAGWYXJBLD', 'OQIS4CBVG1TNQCRQMWOBHLOCZMCP5ZKPCF1AMXBS13EI5MEE'),
					array('44KWZR2C3HVDWSTGPEOEHFTNA2DHL32BKDCWUJC3HD1ZDHZF', 'LXSUSXZWIMARWLTVBZT3HVUYOA5RN0SNR1NUJF4AFRQRBWQ4'),
					array('A2BZQ3VFIILKB0KA1BMLV1DXS5M0E3BSNRW1FVDOXI20OELK', 'OWKK1WFBYICBUBL0VC5UF4UNTHAC0TPE0LY2LJW1C1EYN31I'),
					array('AXXY1AEIL1MVUIS2JKJTSJEMBLKX0IFE223EDVQPZFBR42QB', 'U5TQYKX3F1CNOVH1PT5QCKSM4WSL2H0NEKXUGRCHTOJTYHIB'),
					array('A4NVEI2FKX3QR5CBC24S4TIKTY1WXWJ2ZSO5VPGLMKARPM0I', 'QODM2JM2Z4BVZ5DXVI0F2U050DSNEN2B2B5LHTDTOLUD5CFX')
				);

/** @var API Key di acak */
$_api_key = $_key_bank[array_rand($_key_bank)];

//print_r($_api_key);

echo 'API Client: '.$_api_key[0];
echo '<br />';
echo 'API Secreet: '.$_api_key[1];

?>