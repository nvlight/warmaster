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

$out_str = <<<OS
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h1><p>Ошибка соединения с БД</p></h1>
</body>
</html>
OS;

if ($mysql['success'] === 0) die($out_str);

// с двойными кавычками не работает !
$mysql['connect']->query('SET NAMES \'utf8\'');

return $mysql;