<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 14:07
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';

//
//
//
function update_user_resourses($dbh, $user_id, $res)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, ресурсы НЕ обновлены!',];
    $sql = "UPDATE user SET resourses = '$res' WHERE id = $user_id";
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, ресурсы обновлены!',];

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
//
//
function get_user_resourses($dbh, $user_id)
{
    //
    $sql = "SELECT resourses as `res` FROM user WHERE id = $user_id";
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
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

//
//
//
function get_user_by_mail($dbh, $mail)
{
    //
    $sql = "SELECT id, username, userpassword, mail FROM user WHERE mail = '{$mail}'";
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

//
function set_startup_resourses($dbh, $user_mail)
{
    //
    $user = get_user_by_mail($dbh, $user_mail);
    if ($user['success'] === 1){
        //
        $user = $user['res'][0];

        $startup_user_res = [];
        $startup_user_res['id'] = $user['id'];
        $startup_user_res['username']     = $user['username'];
        $startup_user_res['userpassword'] = $user['userpassword'];
        $startup_user_res['mail']         = $user['mail'];
        $startup_user_res['gold'] = 500;
        $startup_user_res['stage'] = 0;
        $res_st = $startup_user_res;
        $res_st_json = json_encode($res_st);

        $upd = update_user_resourses($dbh, $user['id'], $res_st_json);
        if ($upd['success'] === 1){

        }
    }


}


$resourses = json_encode(['user' => 'ivi', 'gold' => 555]);
//echo Debug::d($resourses,'$resourses',1);

//$upd_user_res = update_user_resourses($mysql['connect'], 61, $resourses);
//echo Debug::d($upd_user_res);

//
//echo Debug::d(get_user_resourses($mysql['connect'], $_SESSION['user']['id']),'', 1 );

//
$some_mail = 'iduso@mail.ru';
//echo Debug::d(get_user_by_mail($mysql['connect'], $some_mail),'',1);

echo Debug::d(set_startup_resourses($mysql['connect'], 'iduso@mail.ru'),'');