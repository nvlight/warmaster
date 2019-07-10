<?php

//ini_set('display_errors', 'On');
//ini_set('error_reporting', E_ALL);
session_start();

// нужно добавить защиту от вызова извне.

if (!isset($_SESSION))
{
    $rs = ['success' => 0, 'message' => 'Start without session!'];
    die(json_encode($rs));
}

//
if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'Start without session!'];
    die(json_encode($rs));
}

// нужно добавить защиту от единовременного вызова

putenv('GDFONTPATH=' . realpath('.'));
$dir = 'arial';

//echo(__FILE__);die;
require './functions.php';

// 
$captcha_item_count = 6; 
$words = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'; 
$captcha_text = '';

for($i=1; $i<=6; $i++){	
	$captcha_numbers = rand(1,9);
	$captcha_text .= $words[rand(0,strlen($words)-1)];
}

$_SESSION['captcha2'] = $captcha_text;
//echo Debug::d($captcha_text);

$all_colors = json_decode(file_get_contents('all_colors.json'));
//echo Debug::d($all_colors,'');
$rand_color = $all_colors[rand(0,count($all_colors)-1)];

// for($i=0;$i<=100;$i++){
// $rand_color = $all_colors[rand(0,count($all_colors)-1)];
// echo Debug::d($rand_color,'');
// }

//
function convert_hexcolor2dec($color)
{
	// $rand_color
	// we have something like this --> @34Feac
	// we need to convert it to 200,100,90
	// 3 двойки нужно разбить 
		
	$fd = hexdec(mb_substr($color,1,2));
	$sd = hexdec(mb_substr($color,3,2));
	$th = hexdec(mb_substr($color,5,2));
	// echo Debug::d(($fd),'');
	// echo Debug::d(($sd),'');
	// echo Debug::d(($th),'');
	return [$fd, $sd, $th];
}
$fixed_color = "#B56ADA"; 
$main_color = convert_hexcolor2dec($fixed_color);

$img['width']  = 85;
$img['height'] = 29;
$img['resourse2'] = imagecreatetruecolor($img['width'], $img['height']);

$rand_color_converted = imagecolorallocate($img['resourse2'], $main_color[0], $main_color[1], $main_color[2]); 
$black = imagecolorallocate($img['resourse2'], 0, 0, 0);
$color = imagecolorallocate($img['resourse2'], 200, 100, 90); // red
$white = imagecolorallocate($img['resourse2'], 255, 255, 255);

imagefilledrectangle($img['resourse2'],0,0,399,99,$white);

$angle_array = [];
for($i=0;$i<=15;$i++)
	$angle_array[] = $i;

$captcha = [];
for($i=0;$i<mb_strlen($_SESSION['captcha2']);$i++)
	$captcha[] = $_SESSION['captcha2'][$i];
//echo Debug::d($captcha,'');

$x = 0; $y = 20; 
foreach($captcha as $k => $v){
	$angle = $angle_array[rand(0,count($angle_array)-1)];
	imagettftext($img['resourse2'], 17, -$angle, $x, $y, $rand_color_converted, $dir, $v);	
	$x+=13; //$y+=20;
	//echo $v . "<br>";
}

//
function draw_line($img, $color='')
{
	$img_line['x_offset'] = 5;
	$img_line['y_offset'] = 3;
	$img_line['x1'] = rand(0, $img_line['x_offset']);
	$img_line['y1'] = rand(0, $img['height']);
	$img_line['x2'] = rand($img['width']-$img_line['x_offset'], $img['width']);
	$img_line['y2'] = rand(0, $img['height']);

	if ($color !== ''){
		$mc = convert_hexcolor2dec($color);

		$img_line['color'] = imagecolorallocate($img['resourse2'], $mc[0], $mc[1], $mc[2]); 
	}else{
		$img_line['color'] = imagecolorallocate($img['resourse2'], 200, 100, 90); // red
	}	

	return imageline($img['resourse2'], $img_line['x1'] , $img_line['y1'] , $img_line['x2'] , $img_line['y2'] , $img_line['color'] );
}
draw_line($img,$fixed_color);
draw_line($img,$fixed_color);
draw_line($img,$fixed_color);

//
header("Content-type: image/png");
imagepng($img['resourse2']);
imagedestroy($img);


// тут пример с мануала.
//$im = imagecreatetruecolor(400, 30);
//header("Content-type: image/png");
//$white = imagecolorallocate($im, 255, 255, 255);
//$grey = imagecolorallocate($im, 128, 128, 128);
//$black = imagecolorallocate($im, 0, 0, 0);
//imagefilledrectangle($im, 0, 0, 399, 29, $white);
//$text = 'test...'; $captcha_numbers = rand(1,9);
//$text .= $captcha_numbers;
//$font = 'arial';
//putenv('GDFONTPATH=' . realpath('.'));
//imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);
//imagettftext($im, 20, 0, 10, 20, $black, $font, $text);
//imagepng($im);
//imagedestroy($im);

?>