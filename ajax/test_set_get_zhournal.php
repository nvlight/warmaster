<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

$curr_stage = <<<ZH
'<ul class="Horinis">
 	<li><span class="QuestTitle">Хоринис</span><br> - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу</li>
</ul>'
ZH;

$user_id = $_SESSION['user']['id'];
//
function zhournal_set($dbh, $user_id, $zhournal){
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, журнал НЕ получен!',];
    $sql = "UPDATE user SET zhournal = $zhournal WHERE id = " . intval($user_id);
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, журнал получен!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function zhournal_get($dbh, $user_id){

    //
    $sql = "SELECT zhournal FROM user WHERE id = " . intval($user_id);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, ресурсы найдены!',
                'res' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 0,
                'message' => 'Запрос выполнен, ресурсы НЕ найдены!',
            ];
        }
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }

    return $rs;

}

$zh_set = zhournal_set($mysql['connect'], $user_id, $curr_stage);
echo Debug::d($zh_set,'zh_set');

$zh_get = zhournal_get($mysql['connect'], $user_id);
echo Debug::d($zh_get,'$zh_get');

?>