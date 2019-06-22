<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.05.2019
 * Time: 20:07
 */

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';

//
//
// тут нужно добавить функцию, которая узнает, занят ли текущий емайл
function is_email_duplicate($mysql, $useremail){

    $sql = $mysql['connect']->prepare('SELECT LCASE(`mail`) FROM user WHERE LCASE(mail) = LCASE(?)' );
    try{
        $rs = $sql->execute([$useremail]);
        $rs_count = count($sql->fetchAll(MYSQLI_NUM));
        //echo Debug::d($rs_count,'',2);

        if ($rs_count === 1){
            $rs = [
                'success' => 1,
                'message' => 'Данный email занят!',
            ];
        }else{
            $rs = [
                'success' => 0,
                'message' => 'Данный email свободен!',
            ];
        }

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка. Попробуйте позднее.'
        ];
    }
    return $rs;

}
$email = '1iduso@mail.ru';
$rs = is_email_duplicate($mysql, $email);
echo Debug::d($rs);