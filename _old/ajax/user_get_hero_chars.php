<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

$user_id = $_SESSION['user']['id'];
$dbh = $mysql['connect'];

$rs = get_hero_chars($dbh, $user_id);
//echo Debug::d($rs, 'get hero chars');
die(json_encode($rs));

?>