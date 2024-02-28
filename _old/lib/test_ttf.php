<?php
// Тип содержимого
header('Content-Type: image/png');

// Создание изображения
$im = imagecreatetruecolor(400, 30);

// Создание цветов
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 399, 29, $white);

// Текст надписи
$text = 'test...';
// Замена пути к шрифту на пользовательский
$font = 'arial';
putenv('GDFONTPATH=' . realpath('.'));

// Тень
imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

// Текст
imagettftext($im, 20, 0, 10, 20, $black, $font, $text);

imagepng($im);
imagedestroy($im);
?>