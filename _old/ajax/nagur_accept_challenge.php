<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:43
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';
require '../ajax/db_functions_part_2.php';


if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $rs = ['success' => 0, 'message' => 'something gone wrong again!'];
    die(json_encode($rs));
}


// prepare need form keys and patterns for checking...
$need_form_keys = [
    //['stage','stage','^\d{1,5}$','Введите текущий прогресс!'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],

];
$additional_form_keys = [
    // empty

];

//echo Debug::d($_POST);

/////
check_params($need_form_keys, $additional_form_keys);

//
//echo Debug::d($_REQUEST);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$rs = nagur_accept_challenge($dbh,$user_id);

//echo Debug::d($rs);
die(json_encode($rs));