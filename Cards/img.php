<?php
session_start();
// Définition du content-type
//header('Content-Type: image/png');

// Création de l'image
$im = imagecreatetruecolor(450, 200);

// Création de quelques couleurs
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 68, 68, 68);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 450, 200, $white);



// Remplacez le chemin par votre propre chemin de police
$font = dirname(__FILE__) . '/Momt___.ttf';//'./fontcapchat.ttf';

// Ajout d'ombres au texte
imagettftext($im, 80, rand(-3,3), 20, 150, $grey, $font, $_SESSION['vlg']);

for($a=0; $a < 100; $a++) {imageline($im, 10, rand(10,190), 440, rand(10,190), (($a%2 == 1) ? $black:$grey)) ;}

// Ajout du texte
imagettftext($im, 80, rand(-3,3), 20, 140, $black, $font, $_SESSION['vlg']);

for($a=0; $a < 100; $a++) {imageline($im, rand(10,440), rand(10,190), rand(10,440), rand(10,190), (($a%2 == 1) ? $black:$grey)) ;}

// Utiliser imagepng() donnera un texte plus claire,
// comparé à l'utilisation de la fonction imagejpeg()
imagepng($im);
imagedestroy($im);
?>
