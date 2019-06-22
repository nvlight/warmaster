<?php

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

//
//$username     = $_POST['username'];
//$userpassword = $_POST['userpassword'];

$username = 'iduso@mail.ru';
$userpassword = '1111';

$logined = login($mysql, $username, $userpassword);

echo Debug::d($logined,'logined', 1);

?>