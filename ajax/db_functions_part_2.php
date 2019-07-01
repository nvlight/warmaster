<?php

/// # Debug right block
///
function hero_get_chars($dbh, $i_user)
{
    //
    $sql = "SELECT * 
    FROM hero_info 
    WHERE i_user = " . intval($i_user);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, ресурсы найдены!',
                'res' => $sql_rs2[0]
            ];
        }else{
            $rs = [
                'success' => 0,
                'message' => 'Запрос выполнен, ресурсы НЕ найдены!',
            ];
        }
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }

    return $rs;
}

///
function hero_get_chars_asHtml($array)
{
    $str = "";

    foreach($array as $ak => $av){
        $new_row = <<<NEW_ROW
<strong>{$ak}</strong>: {$av} <br>
NEW_ROW;
        $str .= $new_row;
    }

    $rs = ['success' => 1, 'message' => 'Данные скомпанованы', 'str' => $str];

    return $rs;
}

/// hero_gold_set_withDec
///
///
function hero_gold_set_withDec($dbh, $i_user, $dec_value)
{
    //
    $sql = "UPDATE hero_info SET gold = gold - {$dec_value} WHERE i_user = " . intval($i_user);
    //echo $sql;
    try{
        $dbh->exec($sql);
        $rs = ['success' => 5, 'message' => 'Запрос выполнен, золото установлено!'];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

/// senteza_first_speak
///
///
function senteza_speak($dbh, $i_user, $choise){

    $stage = hero_get_chars($dbh, $i_user);
    if ($stage['success'] === 0) { return $stage; }

    $stager = intval($stage['res']['stage']);

    // будем работать с соответствии с текущим прогрессом - stage
    switch($stager){
        case 1:
            if ($choise === 1 ){
                $message2 = '<p>' . 'Сентеза: Такой разговор мне по душе, можешь проходить :)' . '</p>';

                $curr_gold = user_get_gold($dbh, $i_user);
                if ($curr_gold['success'] === 0){ return $curr_gold; }
                $gold = intval($curr_gold['res'][0]['gold']);
                $minus_value = 100;
                if ( ($gold - $minus_value) < 0 ){
                    $message = '<p>' . 'Сентеза: У тебя и 100 монет не наберется, пошел прочь оборванец!' . '</p>';
                    return ['success' => 1, 'message' => $message];
                }else{
                    $new_gold = $gold - $minus_value;
                    $usg = user_set_gold($dbh, $i_user, $new_gold);
                    if ($usg['success'] === 0 ){ return $usg;}
                    $uss = user_set_stage($dbh, $i_user, 2);
                    if ($uss['success'] === 0 ){ return $uss;}

                    // добавлю сообщение в журнал
                    $message = <<<MESSAGE
<ul class="OnarsFarm">
	<li><span class="QuestTitle">Ферма Онара</span><br>
	- Меня пропустили на ферму, теперь я могу заработать немного денег в полях. Но для этого пришлось отвалить Сентезе 100 золотых, чертов ублюдок! </li>
</ul>
MESSAGE;
                    $rs = journal_add_message($dbh, $i_user, $message);
                    if ($rs['success'] === 0 ) die(json_encode($rs));

                    $rs = journal_get_all_messages($dbh, $i_user);
                    if ($rs['success'] === 0 ) die(json_encode($rs));

                    // сборка всех сообщений в 1
                    $msgs = '';
                    foreach($rs['result'] as $k => $v){
                        $msgs .= $v['message'];
                    }
                    $rs['msgs'] = $msgs;
                    return ['success' => 1, 'message' => $message2, 'gold' => $new_gold, 'msgs' => $msgs];
                }
                // -100 gold
                // hero_gold_set_withDec($dbh, $i_user, $dec_value)
                return ['success' => 1, 'message' => $message];
            } elseif ($choise === 2){
                $message2 = '<p>' . "Сентеза избил тебя и забрал все деньги!" . '</p>';

                // set gold to zero (0)
                $usg = user_set_gold($dbh, $i_user, 0);
                if ($usg['success'] === 0 ){ return $usg;}
                //
                $uss = user_set_stage($dbh, $i_user, 3);
                if ($uss['success'] === 0 ){ return $uss;}

                // добавлю сообщение в журнал
                $message = <<<MESSAGE
<ul class="OnarsFarm">
	<li><span class="QuestTitle">Ферма Онара</span><br>
	- Меня пропустили на ферму, теперь я могу заработать немного денег в полях. Этот ублюдок, Сентеза навалял мне по полной и отжал все бабло! </li>
</ul>
MESSAGE;
                $rs = journal_add_message($dbh, $i_user, $message);
                if ($rs['success'] === 0 ) die(json_encode($rs));

                $rs = journal_get_all_messages($dbh, $i_user);
                if ($rs['success'] === 0 ) die(json_encode($rs));

                // сборка всех сообщений в 1
                $msgs = '';
                foreach($rs['result'] as $k => $v){
                    $msgs .= $v['message'];
                }
                $rs['msgs'] = $msgs;

                return ['success' => 1, 'message' => $message2, 'gold' => 0, 'msgs' => $msgs];
            }else{
                return ['success' => 0, 'message' => 'Ответ Сентезы не входит в допустимый список'];
            }
            break;
        case 2:
            $message = '<p>' . 'Сентеза: С тобой приятно иметь дело :)' . '</p>';
            return ['success' => 1, 'message' => $message];
            break;
        case 3:
            $message = '<p>' . 'Сентеза: С тобой приятно иметь дело :)' . '</p>';
            return ['success' => 1, 'message' => $message];
            break;


        default: return ['success' => 0, 'message' => 'Этот уровень в списках не присутствует...'];
    }

}

/// Дополнительная вспомогательная функция
///
///

/// Equipment is exists?
///
///
function equipment_is_exists($dbh, $i_user){

    $sql = "SELECT count(*) cnt FROM equipment WHERE i_user = " . intval($i_user);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));

        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, найдено!',
                'result' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 2,
                'message' => 'Запрос выполнен, НЕ найдено!',
            ];
        }
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

/// Equipment is exists by i_item_type
///
///
function equipment_is_exists_by_item_type($dbh, $i_user, $i_item_type){

    $sql = "
        SELECT 
            shop_item.i_item_type item_type
        FROM 
            equipment        
        left JOIN shop_item on shop_item.id = equipment.i_item        
        WHERE 
            i_user = " . intval($i_user) . " and i_item_type = {$i_item_type} ";
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));

        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, найдено!',
                'result' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 2,
                'message' => 'Запрос выполнен, НЕ найдено!',
            ];
        }
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}


///
///
///
//
function user_set_gold_withInc($dbh, $i_user, $gold)
{
    //
    $sql = "UPDATE hero_info SET gold = gold + {$gold} WHERE i_user = " . intval($i_user);
    //echo $sql;
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, золото установлено!', 'gold' => $gold];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

///
///
///
function quest_the_lost_peoples($dbh, $i_user){

    $message = <<<MESSAGE
<ul class="LostPeopleQuest">
	<li> 
		<span class="QuestTitle">Где все пропавшие люди?</span> 
		<br>
		 - С фермы Онара пропадают люди, надо разобраться 		 
	</li>
</ul>
MESSAGE;

    $curr_stage = user_get_stage($dbh, $i_user);
    if ($curr_stage['success'] === 0) return $curr_stage;
    $real_stage = intval($curr_stage['res'][0]['stage']);

    $new_stage = 4;
    switch($real_stage){
        case 2:
        case 3:
            $uss = user_set_stage($dbh, $i_user, $new_stage);
            if ($uss['success'] === 0) { return $uss; }
            //
            $rs = journal_add_message($dbh, $i_user, $message);
            if ($rs['success'] === 0 ) die(json_encode($rs));

            $rs = journal_get_all_messages($dbh, $i_user);
            if ($rs['success'] === 0 ) die(json_encode($rs));

            // сборка всех сообщений в 1
            $msgs = '';
            foreach($rs['result'] as $k => $v){
                $msgs .= $v['message'];
            }
            $uss['msgs'] = $msgs;
            return die(json_encode($uss));
            break;
        default: return die(json_encode(['success' => 0, 'message' => 'default']));
    };

    return ;
}


///
///
///
function journal_add_message($dbh, $i_user, $message)
{
    try{
        $stmt = $dbh->prepare("INSERT INTO game_journal (i_user, message) VALUES (:i_user, :message)");

        $stmt->bindParam(':i_user', $i_user);
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

}


/// journal_get_all_messages
///
///
function journal_get_all_messages($dbh, $i_user)
{
    $sql = "SELECT * FROM game_journal WHERE i_user = " . intval($i_user);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));

        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, найдено!',
                'result' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 2,
                'message' => 'Запрос выполнен, НЕ найдено!',
            ];
        }
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}