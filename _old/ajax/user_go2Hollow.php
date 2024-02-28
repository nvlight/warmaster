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
require '../ajax/db_functions_part_2.php';

//
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

$stage = user_get_stage($dbh, $user_id)['res'][0]['stage'] * 1;
//echo Debug::d($stage);
if ($stage['success'] === 0) { return $stage; }

//
$min_health = $params['hallow_min_health'];

$eating_cost = $params['eating_cost'];
//echo Debug::d($eating_cost,'eating_cost');

///
///
///
function drop_items($dbh, $user_id, $min_health){
    $upd_health = user_set_health($dbh, $user_id, $min_health);
    if ($upd_health['success'] === 0) { return $upd_health; }

    // get equipments
    $eie = equipment_is_exists($dbh, $user_id);
    if ($eie['success'] === 0) { return $eie; }
    //echo Debug::d($eie,'$eie',2);

    $eie_count = intval($eie['result'][0]['cnt']);
    //echo Debug::d($eie_count,'$eie_count',2);
    $eie_count = ($eie_count >= 1) ? 1 : 0;

    if ($eie_count === 1){
        // теперь нужно сбросить снаряжение и соответственно отнять характеристики героя, также нужно
        // на клиенте снять снаряжение с героя, обновить таблицу с характеристиками героя

        // появилась проблема, мы не знаем какие у героя итемы на экиппировку, поэтому получаем ошибку
        // при попытке сброса не существующего итема...

        // проверяем сначала существуют ли итемы
        $eiebit1 = equipment_is_exists_by_item_type($dbh, $user_id, 1);
        $eiebit2 = equipment_is_exists_by_item_type($dbh, $user_id, 2);
        if ($eiebit1['success'] === 0) { return $eiebit1; }
        if ($eiebit2['success'] === 0) { return $eiebit2; }

        //
        if ($eiebit1['success'] === 1){
            $di1 = equipment_drop_item_in_fight_by_type($dbh, $user_id, 1);
            if ($di1['success'] === 0) { return $di1; }
        }
        if ($eiebit2['success'] === 1){
            $di2 = equipment_drop_item_in_fight_by_type($dbh, $user_id, 2);
            if ($di2['success'] === 0) { return $di2; }
        }

        $upd_health['drop_items'] = 1;
        $upd_health['equip_count'] = $eie_count;

        $upd_health['message'] = '<p>Ты почти захлебнулся в трясине, но чудом спасся освободившись от тянущего на дно снаряжения. Здоровье на минимуме!</p>';
    }else{
        $upd_health['message'] = '<p>Ты едва не захлебнулся в трясине. Здоровье на минимуме!</p>';
        $upd_health['equip_count'] = $eie_count;
    }
    return $upd_health;
}

try{
    switch ($stage){
        case 1:
        case 2:
        case 3:
        case 4:
        case 5:
            /// тут сначала нужно определить с картой, если у нас есть карта, то идем дальше
            /// иначе как обычно, сбрасываем итемы если есть
        case 6:
        case 7:
        case 8:
        case 9:
        case 10:
        case 11:
            $rs = drop_items($dbh, $user_id, $min_health);
            die(json_encode($rs));
            //$rs = ['success' => 1, 'message' => 'Тут должен быть орк и тдп'];
            //die(json_encode($rs));
        // тут нужно добавлять stage пока у нас не будет карта )
        default:
            $rs = ['success' => 1, 'message' => 'default'];
            die(json_encode($rs));
    }

}catch (Exception $e){
    $rs = [
        'success' => 0,
        'message2' => $e->getMessage() . ' : ' . $e->getCode(),
        'message' => 'Ошибка при запросе. Попробуйте позднее.'
    ];
}
die(json_encode($rs));