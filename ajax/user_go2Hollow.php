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
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$stage = user_get_stage($dbh, $user_id)['res'][0]['stage'] * 1;
//echo Debug::d($stage);

//
$min_health = $params['hallow_min_health'];

$eating_cost = $params['eating_cost'];
//echo Debug::d($eating_cost,'eating_cost');

try{
    switch ($stage){
        case 1:
                $upd_health = user_set_health($dbh, $user_id, $min_health);
                $upd_health['message'] = '<p>Ты едва не захлебнулся в трясине. Здоровье на минимуме!</p>';
                break;
        default:
                $upd_health = ['success' => 0, 'message' => 'default'];
    }

    $rs = $upd_health;

}catch (Exception $e){
    $rs = [
        'success' => 0,
        'message2' => $e->getMessage() . ' : ' . $e->getCode(),
        'message' => 'Ошибка при запросе. Попробуйте позднее.'
    ];
}
die(json_encode($rs));