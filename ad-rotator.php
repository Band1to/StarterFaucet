<?php

$ads_array = array(
	'<iframe src="http://adalso.com/ad/pbnr1.php?ref=1371" marginwidth="0" marginheight="0" width="728" height="90" scrolling="no" border="0" frameborder="0"></iframe>',
	"<iframe data-aa='115888' src='https://ad.a-ads.com/115888?size=728x90' scrolling='no' style='width:728px; height:90px; border:0px; padding:0;overflow:hidden' allowtransparency='true' frameborder='0'></iframe>"
);

function rotate() {
	global $ads_array;
	return $ads_array[array_rand($ads_array)];
}

function rotate_top() {
	global $ads_array;
	return $ads_array[array_rand($ads_array)];
}

$ads_left = array(
	"<iframe data-aa='115904' src='https://ad.a-ads.com/115904?size=120x600' scrolling='no' style='width:120px; height:600px; border:0px; padding:0;overflow:hidden' allowtransparency='true' frameborder='0'></iframe>"
);

function rotate_left() {
	global $ads_left;
	return $ads_left[array_rand($ads_left)];
}

$ads_right = array(
	"<iframe data-aa='115914' src='https://ad.a-ads.com/115914?size=160x600' scrolling='no' style='width:160px; height:600px; border:0px; padding:0;overflow:hidden' allowtransparency='true' frameborder='0'></iframe>"
);

function rotate_right() {
	global $ads_right;
	return $ads_right[array_rand($ads_right)];
}

?>
