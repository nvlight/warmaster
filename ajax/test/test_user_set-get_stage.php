<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 15:45
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

$user_id = 69;
$dbh = $mysql['connect'];

$curr_stage = user_get_stage($dbh, $user_id);
echo Debug::d($curr_stage);

$custom_stage = 9;
$uss = user_set_stage($dbh, $user_id, $custom_stage);
echo Debug::d($uss);
