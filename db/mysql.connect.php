<?php

$dsn = "mysql:host=" .HOST. "; dbname=".DBNAME."; port=". MYSQL_PORT ." charset=".CHARSET;
$mysql = [];
try{
    $mysql['connect'] = new PDO($dsn, USER, PASSWORD);
    $mysql['success'] = 1;
    $mysql['message'] = 'success connect to db';

    $mysql['connect']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $mysql['connect'] = null;
    $mysql['success'] = 0;
    $mysql['message'] = $e->getMessage();
}

// с двойными кавычками не работает !
$mysql['connect']->query('SET NAMES \'utf8\'');

return $mysql;