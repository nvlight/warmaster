<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:43
 */

session_start();

$params = require '../config/params.php';
require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

//
//$stage = intval($_SESSION['user']['rs']['stage']);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$curr_gold = user_get_gold($dbh, $user_id)['res'][0]['gold'];
//echo Debug::d($curr_gold);
$eating_cost = $params['eating_cost'];
//echo Debug::d($eating_cost,'eating_cost');

try{
    //
    if ( ($curr_gold - $eating_cost) >= 0){
        $new_gold = $curr_gold - $eating_cost;
        $max_health = $params['max_health'];
        user_set_gold($dbh, $user_id, $new_gold);
        user_set_health($dbh, $user_id, $max_health);
        $rs = [
            'success' => 2,
            'message' => 'Здоровье полностью восстановлено!',
            'gold' => $new_gold,
            'health' => $max_health,
        ];
    }else{
        $rs = [
            'success' => 1,
            'message' => 'Селина: твоих денег не достаточно для оплаты :) ',
        ];
    }

}catch (Exception $e){
    $rs = [
        'success' => 0,
        'message2' => $e->getMessage() . ' : ' . $e->getCode(),
        'message' => 'Ошибка при запросе. Попробуйте позднее.'
    ];
}
die(json_encode($rs));