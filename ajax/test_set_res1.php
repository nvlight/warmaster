<?php
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
    echo Debug::d($sql,'');
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

$new_res = $_SESSION['user']['gold'] = 700;
$new_res = $_SESSION['user'];

        $_SESSION['user']['zhournal'] = <<<ZH
'<li>' + Horinis + '<br>' + ' - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу' + '</li>'
ZH;
        //$_SESSION['user']['zhournal'] = json_encode($_SESSION['user']['zhournal']);
unset($_SESSION['user']['zhournal']);

$new_res = json_encode($new_res);
//unset($_SESSION['user']);
$rs = update_user_resourses($mysql['connect'], $_SESSION['user']['id'], $new_res);

// {"id":"65","username":"Kep","userpassword":"1111","mail":"iduso@mail.ru","gold":500,"stage":0}
$corr_json_encode = "{\"id\":\"65\",\"username\":\"Kep\",\"userpassword\":\"1111\",\"mail\":\"iduso@mail.ru\",\"gold\":500,\"stage\":0}";
$curr['user'] = [
    'id' => 64,
    'username'=> 'Kep',
    'userpassword'=> 1111,
    'mail'=> 'iduso@mail.ru',
    'gold'=> 500,
    'stage' => 0,
];

echo Debug::d($rs,'rs');