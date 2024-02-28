<?php

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);

session_start();
//unset($_SESSION['user']);
defined('app_start') or define('app_start', 1);
$_SESSION['app_start'] = 1;

require 'config/main.php';
$params = require 'config/params.php';
require 'lib/singleton.php';
require 'lib/functions.php';
require 'db/mysql.config.php';
require 'db/mysql.connect.php';
require 'ajax/db_functions_part_1.php';
require 'ajax/db_functions_part_2.php';


$app = WarMaster::app();
$app->set('username', 'ivan');
WarMaster::app()->set('userpassword', 'balanar');
WarMaster::app()->set('mysql', $mysql);
//die(Debug::d(WarMaster::app(),'warmaster_singletone...'));

// default page
$main_filename = "web/layouts/auth.php";



//echo Debug::d($_SESSION);

//
if (array_key_exists('user', $_SESSION))
{
    // т.е. у нас экран приветствия,
    //$js1 = require 'lib/create_js1.php';
    $dbh = $mysql['connect'];
    $i_user = $_SESSION['user']['id'];

    $user_get_equipment = user_get_equipment($dbh, $_SESSION['user']['id']);
    require './lib/warmaster/shops.php';

    // подключаем файл, в котором будут тестироваться запросы к БД
    require './lib/test_queries_to_db.php';

    $user_data = hero_get_chars($dbh, $i_user);

    $main_filename = "web/layouts/main.php";
}

//
if (file_exists($main_filename))
    require $main_filename;
?>