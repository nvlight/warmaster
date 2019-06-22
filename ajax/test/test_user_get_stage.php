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
require '../ajax/db_functions_part_1.php';

//
//$stage = intval($_SESSION['user']['rs']['stage']);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];
$rs = user_get_stage($dbh,$user_id);
echo Debug::d($rs);

die(json_encode($rs));
