<?php

require '../lib/functions.php';
require '../db/mysql.config.php';
require '../db/mysql.connect.php';

//echo Debug::d($mysql);

$user_data = ['ivan','iPaa@@Sss1', 'ivi@gmail.com'];
$result = add_new_warmaster_user($mysql, $user_data);

function add_new_warmaster_user($mysql, $user_data){
    $sql = $mysql['connect']->prepare('INSERT INTO user (username, userpassword, mail) VALUES (?,?,?)' );
    try{
        $rs = $sql->execute($user_data);
        $rs = [
            'success' => 1,
            'message' => 'Пользователь зарегистрирован!',
        ];
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка. Попробуйте позднее.'
        ];
    }
    return $rs;
}

echo Debug::d($result,'',2);