<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:43
 */

session_start();


require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

//
//$stage = intval($_SESSION['user']['rs']['stage']);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];
$stage = 0;
$rs = user_set_stage($dbh,$user_id,$stage);
$rs['stage'] = $stage;
echo Debug::d($rs);
die(json_encode($rs));
