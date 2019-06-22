<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:06
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

$user_id = 8;
$dbh = $mysql['connect'];

$curr_gold = user_get_gold($dbh, $user_id);
echo Debug::d($curr_gold,'',2);
//die;

//$custom_gold = intval($curr_gold['res'][0]['gold']) - 100;
$custom_gold = 700;
//echo $custom_gold; die;
$uss = user_set_gold($dbh, $user_id, $custom_gold);
echo Debug::d($uss);

//
$curr_gold = user_get_gold($dbh, $user_id);
echo Debug::d($curr_gold,'',2);
