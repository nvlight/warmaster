<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 12:55
 */

session_start();

unset($_SESSION['user']);

$rs = ['success' => 1, 'message' => 'Вышли из системы!'];

die(json_encode($rs));