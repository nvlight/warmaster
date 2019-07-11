<?php
session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';
require '../ajax/db_functions_part_2.php';

echo Debug::d($_REQUEST);