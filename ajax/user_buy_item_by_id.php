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
require '../ajax/user_db_functions.php';


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
    ['item_id','item_id','^\d{1,5}$','Введите номер товара!'],
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
$i_item = intval($_POST['item_id']);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];
$rs = user_inventory_buy_item($dbh, $user_id, $i_item);
//echo Debug::d($rs);

// get the new inventory after buying item!
$WM_user_inventory = user_inventory_get($dbh);
$rs['inventory'] = $WM_user_inventory;

die(json_encode($rs));