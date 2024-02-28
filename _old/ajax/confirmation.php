<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Подтверждение регистрации - Warmaster</title>
    <style>
        .wrapper{
            width: 80%;
            display: flex;
            padding: 20px;
            align-content: center;
            justify-content: center;
            background-color: #e6d5d5;
            margin: auto;
            flex-direction: column;
        }
    </style>
</head>
<body>

<div class="wrapper">
<h2>Warmaster - подтверждение регистрации</h2>

<?php
session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';
require '../ajax/db_functions_part_2.php';

//echo Debug::d($_REQUEST);

$dbh = $mysql['connect'];
$hash_exists = confirmation_userreg_isExistsHash($dbh, $_GET['hash']);
//echo Debug::d($hash_exists,'',1);
if ($hash_exists['success'] !== 1) {
    ?>
    <p>Ошибка при сравнении хеша</p>
    <?php
    die();
}

$user = $hash_exists['res'];
$i_user = intval($user['id']);
$is_active = intval($user['is_active']);

if($is_active !== 0){
    ?>
    <p>Данный пользователь уже подтвердил свою почту</p>
    <?php
    die();
}

//
$doConfirm = confirmation_userreg_doConfirm($dbh, $i_user);
if ($doConfirm['success'] !== 1){
    ?>
    <p>Ошибка при подтверждении почты, попробуйте повторить позднее или свяжитесь с администратором</p>
    <?php
    die();
}else{
    ?>
    <p>Подтверждение почты успешно выполнено!</p>
    <p>Вперед навстречу новым приключениям, солдат!</p>
    <?php
    die();
}
//echo Debug::d($doConfirm,'',1);

?>

</div>
</body>
</html>