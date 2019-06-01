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
    $rs = ['success' => 0, 'message' => 'something gone wrong again!'];
    die(json_encode($rs));
}

require_once '../vendor/autoload.php';
require_once '../lib/functions.php';

//$rs = ['success' => 122211, 'message' => $_SESSION];
//die(json_encode($rs));

// prepare need form keys and patterns for checking...
$need_form_keys = [        
    ['phone','phone','^\+?\d{1,3}\d{10}$'],
    ['message','message','^[а-яА-Яa-zA-Z\d_.,!?;:\' -]+$'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],
    
];
$additional_form_keys = [
    // empty

];

/////

$subject = "Message from main_site"; 
$msg_header = 'Запрос - получить консультацию!';

check_params($need_form_keys, $additional_form_keys);
mySendMailMessage($subject, $msg_header, $need_form_keys, $additional_form_keys);
