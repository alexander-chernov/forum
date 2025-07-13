<?php



// Creation of the picture, you shouldn't modify the dimensions unless you change the code's length. But well, you can do whatever you want... :)
$largeur=190;	// width
$hauteur=30;	// height
$img = imagecreate ($largeur, $hauteur) or die("Cannot Initialize new GD image stream");

// The colors...
if ($invert) {
    $black = imagecolorallocate($img, 15, 60, 96);		// random background color... (not too dark though)
    $bgc = imagecolorallocate($img, 255, 255, 255);
} else {
    $bgc = imagecolorallocate($img, 15, 60, 96);		// random background color... (not too dark though)
    $black = imagecolorallocate($img, 255, 255, 255);
}

// Let's paint the background
imagefilledrectangle($img, 0, 0, $largeur, $hauteur, $bgc);

// Writes the code
$hor_pos=mt_rand(8,20); // horizontal position
$font_max = 22;
$max_wrap = $largeur/strlen($code) + $font_max/3.6; // - 3.6*$font_max;
$min_wrap = $font_max-2;
//putenv('GDFONTPATH=' . realpath('.'));
for($i=0;$i<strlen($code);$i++) {

	imagettftext($img, rand($font_max-4,$font_max), rand(-20,20), $hor_pos, $hauteur-rand(2,7), $black, './verdana.ttf', $code[$i]);
	//imagestring($img, 5, $hor_pos, mt_rand(2,10), $code[$i], $black);
	//$hor_pos += mt_rand($min_wrap,$max_wrap);
	$hor_pos += 36;
	if ($hor_pos > $largeur - $font_max) $hor_pos = $largeur - (int)($font_max/1.1);
}

// Now we're going to make it hard to read the picture :
// Let's spray some multicolored pixels
for($i=0;$i<500;$i++) {
	imagesetpixel($img, mt_rand(0,$largeur), mt_rand(0,$hauteur), imagecolorallocate($img, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255)));
}
// Let's add some random lines (be careful not to put too many)
for($i=0;$i<8;$i++) {
	if($i<4) {
		$x1=rand(0,$largeur);
		$y1=0;
		$x2=abs($x1-mt_rand(0,5));
		$y2=$hauteur;
	} else {
		$x1=0;
		$y1=rand(0,$hauteur);
		$x2=$largeur;
		$y2=abs($y1-mt_rand(0,5));
	}
	imageline($img, $x1, $y1, $x2, $y2, $black);
}

// Creates the headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: image/png");
imagePNG($img);
imagedestroy($img);

?>