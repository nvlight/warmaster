<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

//
if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

$user_res_keys = [
    'gold', 'health', 'armour_count', 'critical', 'power', 'damage', 'weapon', 'armour_item',
];


//
//
// update SESSION data from DB
$user = get_user_resourses($mysql['connect'], intval($_SESSION['user']['id'] ));
//echo Debug::d($user,'user');
if ($user['success'] === 1){
    //
    $user_res_vals = [];
    $user = $user['res']['res'];
    $user = json_decode($user,1);
    //echo Debug::d($user,'user');

    //echo Debug::d($_SESSION);
    // update SESSION data from DB
    foreach($user_res_keys as $k => $v){
        if (array_key_exists($v, $user)){
            $user_res_vals[$v] = $user[$v];
            //echo $k . ' : ' . $v; echo "<br>";
            $_SESSION['user'][$v] = $user[$v];
        }
    }
    $user_res_vals = ($user_res_vals);
    //echo Debug::d($user_res_vals);
    //echo Debug::d($_SESSION);
    //die;
    $rs = ['success' => 1, 'message' => 'we have new datas', 'data' => $user_res_vals];
    die(json_encode($rs));
}
$rs = ['success' => 0, 'message' => 'not find user'];
die(json_encode($rs));
