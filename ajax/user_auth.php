<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

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
    ['User mail','mail','^[a-zA-Z_]+@[a-zA-Z\d_]+\.[a-zA-Z\d_]+', 'Мейл'],
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

// set SESSION data!
//if ($logined['success'] === 1){
//    $user_id = $logined['rs']['id'];
//    $get_user_res = get_user_resourses($mysql['connect'], $user_id);
//    if ($get_user_res['success'] === 1){
//        //echo Debug::d($get_user_res,'$get_user_res');
//        $user_res = $get_user_res['res']['res'];
//        //echo Debug::d($user_res);
//        $user_res = json_decode($user_res,1);
//        //echo Debug::d($user_res);
//        foreach($user_res as $k => $v){
//            $_SESSION['user'][$k] = $v;
//        }
//    }
//}

//
//$upd_user_res = update_user_resourses($mysql['connect'], $_SESSION['user']['id'], $resourses);
//$get_user_res = get_user_resourses($mysql['connect']);

//echo Debug::d($logined,'logined', 1);
die(json_encode($logined));

?>