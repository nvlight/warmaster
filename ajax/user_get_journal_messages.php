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
$user_id = intval($_SESSION['user']['id']);

//die(json_encode(['success' => 1, 'message' => 'done']));

//
$sql = "SELECT message FROM game_journal WHERE i_user = " . $user_id;
try{
    $sql_rs1  = $dbh->query($sql);
    $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
//    echo Debug::d($sql);
//    echo Debug::d($sql_rs1,'',2);
//    echo Debug::d($sql_rs2,'',1);
    if (count($sql_rs2)){

        $concat = "";
        foreach($sql_rs2 as $k => $v){
            $concat .= $v['message'];
        }
        $rs = [
            'success' => 1,
            'message' => 'Запрос выполнен!',
            'res' => $concat,
        ];
    }else{
        $rs = [
            'success' => 0,
            'message' => 'Запрос выполнен, ничего НЕ найдено!',
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