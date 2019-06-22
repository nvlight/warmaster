<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:43
 */

session_start();

$params = require '../config/params.php';
require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

//
$dbh = $mysql['connect'];
$user_id = intval($_SESSION['user']['id']);

$message = <<<MESSAGE
<ul class="Horinis"><li><span class="QuestTitle">Хоринис</span><br> - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу</li></ul>
MESSAGE;

try{
    $sql = "DELETE FROM game_journal WHERE 1 = 1;";
    $dbh->query($sql);

    $stmt = $dbh->prepare("INSERT INTO game_journal (i_user, message) VALUES (:i_user, :message)");

    $stmt->bindParam(':i_user', $user_id);
    $stmt->bindParam(':message', $message);

    $stmt->execute();

    $rs = [
        'success' => 1,
        'message' => 'Запись в журнал занесен!',
        'inner' => $message
    ];

}catch (Exception $e){
    $rs = [
        'success' => 0,
        'message2' => $e->getMessage() . ' : ' . $e->getCode(),
        'message' => 'Ошибка при запросе. Попробуйте позднее.'
    ];
}
die(json_encode($rs));