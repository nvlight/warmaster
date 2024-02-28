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
require '../ajax/db_functions_part_1.php';

//
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$health = user_get_health($dbh, $user_id)['res'][0]['health'] * 1;
//echo Debug::d($stage);
$max_rest_health = $params['max_rest_health'];

try{

    if ($health < 50){
        $new_set_health = user_set_health($dbh, $user_id, $max_rest_health);
        $new_set_health['health'] = $max_rest_health;
        $new_set_health['message'] = 'Часть здоровья восстановлена';
    }else{
        $new_set_health['message'] = 'Ты отдохнул. Сон восстанавливает не более 50% здоровья';
        $new_set_health['success'] = 1;
    }

    $rs = $new_set_health;
}catch (Exception $e){
    $rs = [
        'success' => 0,
        'message2' => $e->getMessage() . ' : ' . $e->getCode(),
        'message' => 'Ошибка при запросе. Попробуйте позднее.'
    ];
}
die(json_encode($rs));