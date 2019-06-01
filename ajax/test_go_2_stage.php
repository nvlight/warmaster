<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 20:51
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';

//
function update_user_resourses($dbh, $user_id, $res)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, ресурсы НЕ обновлены!',];
    $sql = "UPDATE user SET resourses = '$res' WHERE id = ".intval($user_id);
    echo Debug::d($sql,'sql_part');
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

$_SESSION['user']['stage'] = 0;

// мы на пути в город хоринис, за проход с нас берут 200 золота
$_SESSION['user']['gold'] += 33;
//        $_SESSION['user']['zhournal'] = <<<ZH
//'<li>' + Horinis + '<br>' + ' - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу' + '</li>'
//ZH;
//        $_SESSION['user']['zhournal'] = json_encode($_SESSION['user']['zhournal']);
// save resourses
$new_res = $_SESSION['user'];
$new_res = json_encode($new_res);
$upd_res = update_user_resourses($mysql['connect'], $_SESSION['user']['id'], $new_res);
echo Debug::d($upd_res,'');

//$_SESSION['user']['stage']++;

//
$rs = ['success' => 1, 'message' => 'we recive the current stage!',
    'stage' => $_SESSION['user']['stage'],
];
die(json_encode($rs));

?>