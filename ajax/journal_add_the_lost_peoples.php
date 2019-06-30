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
require '../ajax/db_functions_part_2.php';

//
$dbh = $mysql['connect'];
$user_id = intval($_SESSION['user']['id']);

$message = <<<MESSAGE
<ul class="LostPeopleQuest">
	<li> 
		<span class="QuestTitle">Где все пропавшие люди?</span> 
		<br>
		 - С фермы Онара пропадают люди, надо разобраться 		 
	</li>
</ul>
MESSAGE;

$stage = user_get_stage($dbh, $user_id);
if ($stage['success'] === 0 ) die(json_encode($stage));
$curr_stage = intval($stage['res'][0]['stage']);

if ($curr_stage === 2 || $curr_stage === 3){
$rs = journal_add_message($dbh, $user_id, $message);
if ($rs['success'] === 0 ) die(json_encode($rs));

$rs = journal_get_all_messages($dbh, $user_id);
if ($rs['success'] === 0 ) die(json_encode($rs));

// сборка всех сообщений в 1
$msgs = '';
foreach($rs['result'] as $k => $v){
    $msgs .= $v['message'];
}
$rs['msgs'] = $msgs;
}else{
    $rs = ['success' => 1, 'message' => 'there is not stage 2 or 3...', '$curr_stage' => $curr_stage];
}

die(json_encode($rs));