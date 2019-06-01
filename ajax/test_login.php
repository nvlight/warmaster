<?php

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';

//
//
// ищем пользователя, по логину и паролю
function login($mysql, $username, $userpassword){
    $dbh = $mysql['connect'];

    $sql = $dbh->prepare('SELECT username, userpassword FROM user WHERE username = ? and userpassword = ?' );
    try{
        $rs = $sql->execute([$username, $userpassword]);
        $rs_count = count($sql->fetchAll(MYSQLI_NUM));
        //echo Debug::d($rs_count,'',2);

        $rs = ['success' => 1, 'message' => 'Авторизовались!'];
        if ($rs_count === 0){
            $rs = [
                'success' => 0,
                'message' => 'Неверный логин и/или пароль!',
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

//
//$username     = $_POST['username'];
//$userpassword = $_POST['userpassword'];

$username = '1KeP';
$userpassword = '1111';

$logined = login($mysql, $username, $userpassword);

echo Debug::d($logined,'logined', 1);

?>