<?php
$text = $_GET['t'];
if(strlen($text) > 100) // max limit
	$text = '';

// set font path
putenv('GDFONTPATH=' . __DIR__);

// create image
$image = imagecreatetruecolor(250, 28);

// make the background transparent
imagecolortransparent($image, imagecolorallocate($image, 0, 0, 0));

// set text
$font = getenv('GDFONTPATH') . DIRECTORY_SEPARATOR . 'martel.ttf';
imagettftext($image, 18, 0, 4, 20, imagecolorallocate($image, 240, 209, 164), $font, utf8_decode($text));

// header mime type
header('Content-type: image/gif');

// output image to browser
imagegif($image);
