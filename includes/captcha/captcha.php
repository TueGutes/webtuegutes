<?php
/*
*@author Henrik Huckauf
*/

session_start(); 
unset($_SESSION['captcha_spam']); 

// Variablen
$captcha_bg_img 	= 'bg_captcha_' . mt_rand(0, 4) . '.png'; 		// Pfad zum Hintergrundbild
$captcha_over_img 	= 'bg_captcha_over_' . mt_rand(0, 4) . '.png';	// Pfad zum Bild, das über das Captcha gelegt wird
$font_file 			= dirname(__FILE__).'/DejaVuSans-Bold.ttf'; 	// Pfad zur Schriftdatei
$font_size			= 25; 											// Schriftgröße
$text_angle			= mt_rand(0, 5);								// Schriftwinkel (Werte zwischen 0 und 5)
$text_x				= mt_rand(0, 16);								// X-Position (Werte zwischen 0 und 16)
$text_y				= 35;											// Y-Position
$text_chars 		= 5;											// Länge des Textes
$text_color			= array(0, 0, 0);								// Textfarbe (R, G, B)

// Funktion um zufälligen String zu generieren
function rand_string($length = 5)
{
	$str = array_merge(range('A', 'Z'), range(1, 9));
	for($i = 1; $i <= (count($str) * 2); $i++)
	{
		$swap = mt_rand(0, count($str) - 1);
		$tmp = $str[$swap];
		$str[$swap] = $str[0];
		$str[0] = $tmp;
	}
	return substr(implode('', $str), 0, $length);
}

// Zufälligen Text generieren und in der Session speichern
$text = rand_string($text_chars);
$_SESSION['captcha_spam'] = $text;

// Header: Mitteilen, dass es sich um ein Bild handelt und dass dieses nicht im Cache gespeichert werden soll
header('Expires: Mon, 26 Jul 1990 05:00:00 GMT');
header("Last-Modified: " . date("D, d M Y H:i:s") . " GMT");
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-type: image/png');

// Captcha Bild erstellen, Text schreiben & Bild darüber legen
$img = ImageCreateFromPNG($captcha_bg_img);
$text_color = ImageColorAllocate($img, $text_color[0], $text_color[1], $text_color[2]);
imagettftext($img, $font_size, $text_angle, $text_x, $text_y, $text_color, $font_file, $text);
imagecopy($img, ImageCreateFromPNG($captcha_over_img), 0, 0, 0, 0, 140, 40);

// Ausgabe und Löschen des Bildes
imagepng($img); 
imagedestroy($img); 
?>
