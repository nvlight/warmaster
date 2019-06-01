<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 07.05.2019
 * Time: 0:21
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

$user_id = $_SESSION['user']['id'];

$rs = ['success' => 0, 'message' => 'Failed on getting zhournal'];
$zh_get = zhournal_get($mysql['connect'], $user_id);
//echo Debug::d($zh_get,'$zh_get');

if ($zh_get['success'] === 1){
    $rs = ['success' => 1, 'message' => 'getting user zhournal', 'rs' => $zh_get['res']];

}
die(json_encode($rs));

