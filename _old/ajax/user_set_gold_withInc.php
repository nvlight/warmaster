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
    //['gold','gold','^\d{1,5}$','Введито золото!'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],

];
$additional_form_keys = [
    // empty

];

//echo Debug::d($_POST);

/////
check_params($need_form_keys, $additional_form_keys);

//
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$inc_gold = 100;
$rs = user_set_gold_withInc($dbh,$user_id,$inc_gold);

$new_gold = user_get_gold($dbh,$user_id);
if ($new_gold['success'] !== 0){
    $rs['gold'] = intval($new_gold['res'][0]['gold']);
}else{
    $rs['gold'] = 0;
}


die(json_encode($rs));