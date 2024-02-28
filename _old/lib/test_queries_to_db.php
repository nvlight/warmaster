<?php

//
$dbh = $mysql['connect'];
$i_user = $_SESSION['user']['id'];

//$rs = hero_get_chars($dbh, $i_user);
//echo Debug::d($rs,'$rs', 1); die;

//$rs = equipment_is_exists_by_item_type($dbh, $i_user, 1);
//echo Debug::d($rs,'$rs', 1); die;

//equipment_is_exists_by_item_type($dbh, $i_user, $i_item_type)
//$rs = equipment_is_exists_by_item_type($dbh, $i_user, 1);
//echo Debug::d($rs,'$rs', 1); die;

//$eiebit1 = equipment_is_exists_by_item_type($dbh, 8, 1);
//$eiebit2 = equipment_is_exists_by_item_type($dbh, 8, 2);
//echo Debug::d($eiebit1);
//echo Debug::d($eiebit2);
//die;

//echo Debug::d(equipment_is_exists($dbh, $i_user)); die;

//$message = <<<MESSAGE
//<ul class="LostPeopleQuest">
//	<li>
//		<span class="QuestTitle">Где все пропавшие люди?</span>
//		<br>
//		 - С фермы Онара пропадают люди, надо разобраться
//	</li>
//</ul>
//MESSAGE;
//
//$rs = journal_add_message($dbh, $i_user, $message);
//if ($rs['success'] === 0 ) die(json_encode($rs));
//
//$rs = journal_get_all_messages($dbh, $i_user);
//if ($rs['success'] === 0 ) die(json_encode($rs));
//
//// сборка всех сообщений в 1
//$msgs = '';
//foreach($rs['result'] as $k => $v){
//    $msgs .= $v['message'];
//}
//$rs['msgs'] = $msgs;
//
//echo Debug::d($rs);
//
//die;

/// nagur_map_exists($dbh,$user_id)
//echo Debug::d(nagur_map_exists($dbh,8),'',1); die;
