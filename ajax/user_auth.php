<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $rs = ['success' => 0, 'message' => 'There is no POST!'];
    die(json_encode($rs));
}

// prepare need form keys and patterns for checking...
$need_form_keys = [
    ['User mail','mail','^[a-zA-Z_]+[a-zA-Z_\d]*@[a-zA-Z\d_]+\.[a-zA-Z\d_]+', 'Мейл'],
    ['User password','userpassword','^([a-zA-Z\d@!_-]+){4,33}$', 'Пароль'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],
];
$additional_form_keys = [
// empty

];

//
check_params($need_form_keys, $additional_form_keys);

//
$mail     = $_POST['mail'];
$userpassword = $_POST['userpassword'];

//$username = '1KeP';
//$userpassword = '1111';

$logined = login($mysql, $mail, $userpassword);
if ($logined['success'] === 1){
    foreach($logined['rs'] as $k => $v){
        $_SESSION['user'][$k] = $v;
    }
}

//echo Debug::d($logined,'logined', 1);
die(json_encode($logined));

?>