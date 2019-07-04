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
function quest_the_lost_peoples($dbh, $i_user)
{
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

///
///
///
function senteza_go2_onar($dbh, $i_user){
    // set stage to 5 and Onar is enabled
    $new_stage = 5;
    return user_set_stage($dbh, $i_user, $new_stage);
}

///
///
///
function onar_talk($dbh, $i_user){
    /// надо добавить сообщение в журнал
    /// надо сменить стаде с 5 на 6
    ///
    $message = <<<MESSAGE
<ul class="OnarsQuest">
	<li><span class="QuestTitle">Задание Онара</span>
	<br>
	- Пропавшие Борка и Дерек вовсе не пропали, захватили с собой сундук с золотом Онара и скрылись. Онар уверен, что они прячутся в туманной лощине. Нужно найти их живыми или мертвыми и вернуть сундук с золотом
	<br>
	- Онар за меня поручился, теперь я могу тренироваться у Лареса
	</li>
</ul>
MESSAGE;

    $curr_stage = user_get_stage($dbh, $i_user);
    if ($curr_stage['success'] === 0) return $curr_stage;
    $real_stage = intval($curr_stage['res'][0]['stage']);

    switch($real_stage){
        case 5:
            $new_stage = 6;
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
        //case 6:
        default: return die(json_encode(['success' => 0, 'message' => 'default']));
    };

    return ;
};



/// nagur_map_exists.php
///
///
function nagur_map_exists($dbh, $i_user){

    $i_item = 13;
    $is_map_exists = inventory_exists_item_by_id($dbh, $i_item, $i_user);
    //echo Debug::d($is_map_exists);
    if ($is_map_exists['success'] === 0) return $is_map_exists;

    return $is_map_exists;
}

/// nagur_buy_map
///
///
function nagur_buy_map($dbh, $i_user){

    $map_price = 100;

    $curr_stage = user_get_stage($dbh, $i_user);
    if ($curr_stage['success'] === 0) return $curr_stage;
    $real_stage = intval($curr_stage['res'][0]['stage']);

    if ($real_stage < 6){
        return ['success' => 2, 'Как вы сюда попали то?! Это закрытая территория!'];
    }

    $nagur_map_exists = nagur_map_exists($dbh, $i_user);
    if ($nagur_map_exists['success'] === 0) return $nagur_map_exists;
    if ($nagur_map_exists['success'] === 1) {
        return ['success' => 2, 'Карта топей уже у вас имеется, незачем его еще раз попупать, да и она так не продается!'];
    }

    $curr_gold = user_get_gold($dbh, $i_user);
    if ($curr_gold['success'] !== 1) return $curr_gold;

    $real_gold = intval($curr_gold['res'][0]['gold']);
    if ($real_gold < $map_price){
        return ['success' => 2, 'message' => '<p><b>Нагур:</b> Возвращайся когда будешь достаточно богат для клочка карты</p>'];
    }else{
        /// нужно отнять цену карты -> UPD gold
        $hero_upd_column = 'gold'; $gold2set = $real_gold - $map_price;
        $ushc = hero_set_char_byDec($dbh, $i_user, $hero_upd_column, $map_price);
        if ($ushc['success'] === 0) return $ushc;

        /// нужно занести в журнал, что Нагур продал нам карту, -> UPD zhournal
        $nagurMessage = <<<MESSAGE
<ul class="NagurMapBuyed">
	<li><span class="QuestTitle">Карта топей</span>
		<br>
		- Таинственный Нагур продал мне карту топей, теперь я могу исследовать туманную лощину!	
	</li>
</ul>
MESSAGE;
        $rs = journal_add_message($dbh, $i_user, $nagurMessage);
        if ($rs['success'] === 0 ) die(json_encode($rs));

        $rs = journal_get_all_messages($dbh, $i_user);
        if ($rs['success'] === 0 ) die(json_encode($rs));
        // сборка всех сообщений в 1
        $msgs = '';
        foreach($rs['result'] as $k => $v){
            $msgs .= $v['message'];
        }

        /// нужно в инвентарь занести карту -> add Map 2 Inventory! -> UPD again
        $iaim = inventory_add_item_map($dbh);
        if ($iaim['success'] === 0) return $iaim;

        $new_rs = ['success' => 1, 'message' => '<p><b>Нагур:</b> Удачи!</p>'];
        $new_rs['msgs'] = $msgs;
        $new_rs['gold'] = $gold2set;

        return $new_rs;
    }

}

/// inventory_add_item_map($dbh, $i_user){
///
///
function inventory_add_item_map($dbh){
    $i_item = 13;
    return user_inventory_add_item($dbh, $i_item);
}

/// lares_training
///
///
function lares_training($dbh, $i_user)
{
    $stage = user_get_stage($dbh, $i_user);
    if ($stage['success'] === 0) return $stage;
    $real_stage = intval($stage['res'][0]['stage']);

    switch($real_stage){
        case 1: case 2: case 3: case 4: case 5:
            $msg = '<p>Ларес: Кто ты такой? Я не тренирую всех подряд!</p>';
            $rs = ['success' => 1, 'message' => $msg];
            break;
        case 6:
        case 7:
        case 8:
            /// сначала узнаем кол-во силы, если сила >= 5 то выходим,
            $power = get_hero_chars($dbh, $i_user);
            if ($power['success'] === 0) { return $power;}
            $real_power = intval($power['res']['power']);
            if ($real_power >= 5) {
                $msg = '<p>Ларес: Ты достаточно силен, мне больше нечему тебя учить</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }

            // есть ли оружие у героя, если нет, то сразу выходим
            //$hero_have_weapon = false;
            $weapon_is_exists = equipment_get_one_with_itemAndItemtype($dbh, $i_user, 1);
            if ($weapon_is_exists['success'] === 0) return $weapon_is_exists;

            // т.е. мы без оружия...
            if ($weapon_is_exists['success'] === 2){
                $msg = '<p>Ларес: Онар хорошо отзывался о тебе. У тебя есть оружие? возвращайся когда будет с чем тренироваться!</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }
            // мы с оружием
            $equip_i_item = intval($weapon_is_exists['result']['i_item']);
            if ($weapon_is_exists['success'] === 1) $hero_have_weapon = true;
            else { $hero_have_weapon = false; }
            if (!$hero_have_weapon){
                $msg = '<p>Ларес: Онар хорошо отзывался о тебе. У тебя есть оружие? возвращайся когда будет с чем тренироваться!</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }

            // с дубинкой нельзя на тренировку!
            if ($equip_i_item === 1){
                $msgs = '<p>Ларес: Дубинкой можешь крыс в лесу погонять! Возвращайся с достойным оружием!</p>';
                $rs = ['success' => 1, 'message' => $msgs, 'power' => $real_power];
                break;
            }

            // тренировка стоит 200 монет
            $curr_gold = user_get_gold($dbh, $i_user);
            if ($curr_gold['success'] === 0){ return $curr_gold; }
            $gold = intval($curr_gold['res'][0]['gold']);
            $train_price = 200;
            if ($gold < $train_price){
                $msgs = '<p>Ларес: Тренировка стоит 200 монет, возваращайся когда будет чем платить!</p>';
                $rs = ['success' => 1, 'message' => $msgs, 'gold' => $gold];
                break;
            }else{

                $dinamicHtml = <<<DIN_HTML
<p>Ларес: Тренировка стоит 200 монет </p>
<button class="btn" id="goTrainNow">Тренироваться</button>
DIN_HTML;
                $rs = ['success' => 2, 'message' => 'trainging finally...', 'html' => $dinamicHtml];
                break;
            }

            $rs = ['success' => 1, 'message' => 'Training... but ist cannot be!', 'power' => $real_power];
            break;
        default:
            $rs = ['success' => 1, 'message' => 'default'];

    }
    return $rs;
}

/// lares_soviet
///
///
function lares_soviet($dbh, $i_user)
{
    $stage = user_get_stage($dbh, $i_user);
    if ($stage['success'] === 0) return $stage;
    $real_stage = intval($stage['res'][0]['stage']);

    switch($real_stage){
        case 1: case 2: case 3: case 4: case 5:
            $msg = '<p>Ларес: Думаешь я раздаю советы каждому встречному!</p>';
            $rs = ['success' => 1, 'message' => $msg];
            break;
        case 6:
        case 7:
        case 8:
        case 9:
            $rs = ['success' => 2, 'message' => 'Soviet...', 'stage' => $real_stage];
            break;
        default:
            $rs = ['success' => 1, 'message' => 'default'];

    }
    return $rs;
}

/// lares_real_training()
///
///
function lares_real_training($dbh, $i_user)
{
    $stage = user_get_stage($dbh, $i_user);
    if ($stage['success'] === 0) return $stage;
    $real_stage = intval($stage['res'][0]['stage']);

    switch($real_stage){
        case 1: case 2: case 3: case 4: case 5:
        $msg = '<p>Ларес: Кто ты такой? Я не тренирую всех подряд!</p>';
        $rs = ['success' => 1, 'message' => $msg];
        break;
        case 6:
            /// сначала узнаем кол-во силы, если сила >= 5 то выходим,
            $power = get_hero_chars($dbh, $i_user);
            if ($power['success'] === 0) { return $power;}
            $real_power = intval($power['res']['power']);
            if ($real_power >= 5) {
                $msg = '<p>Ларес: Ты достаточно силен, мне больше нечему тебя учить</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }

            // есть ли оружие у героя, если нет, то сразу выходим
            //$hero_have_weapon = false;
            $weapon_is_exists = equipment_get_one_with_itemAndItemtype($dbh, $i_user, 1);
            if ($weapon_is_exists['success'] === 0) return $weapon_is_exists;

            // т.е. мы без оружия...
            if ($weapon_is_exists['success'] === 2){
                $msg = '<p>Ларес: Онар хорошо отзывался о тебе. У тебя есть оружие? возвращайся когда будет с чем тренироваться!</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }
            // мы с оружием
            $equip_i_item = intval($weapon_is_exists['result']['i_item']);
            if ($weapon_is_exists['success'] === 1) $hero_have_weapon = true;
            else { $hero_have_weapon = false; }
            if (!$hero_have_weapon){
                $msg = '<p>Ларес: Онар хорошо отзывался о тебе. У тебя есть оружие? возвращайся когда будет с чем тренироваться!</p>';
                $rs = ['success' => 1, 'message' => $msg, 'power' => $real_power];
                break;
            }

            // с дубинкой нельзя на тренировку!
            if ($equip_i_item === 1){
                $msgs = '<p>Ларес: Дубинкой можешь крыс в лесу погонять! Возвращайся с достойным оружием!</p>';
                $rs = ['success' => 1, 'message' => $msgs, 'power' => $real_power];
                break;
            }

            // тренировка стоит 200 монет
            $curr_gold = user_get_gold($dbh, $i_user);
            if ($curr_gold['success'] === 0){ return $curr_gold; }
            $gold = intval($curr_gold['res'][0]['gold']);
            $train_price = 200;
            if ($gold < $train_price){
                $msgs = '<p>Ларес: Тренировка стоит 200 монет, возваращайся когда будет чем платить!</p>';
                $rs = ['success' => 1, 'message' => $msgs, 'gold' => $gold];
                break;
            }else{
                $new_gold = 0; $html = '';
                $new_gold = $gold - $train_price;
                $usg = user_set_gold($dbh, $i_user, $new_gold);
                if ($usg['success'] === 0){ return $usg; }

                // теперь нужно увеличить силу героя на 1!
                $type = 'power'; $value = 1;
                $ushcw = user_set_hero_chars_withInc($dbh, $i_user, $type, $value);
                if ($ushcw['success'] === 0 ) return $ushcw;

                $rs = ['success' => 2, 'message' => 'trainging finally...', 'gold' => $new_gold, 'power' => $real_power + 1,];
                break;
            }

            $rs = ['success' => 1, 'message' => 'Training... but ist cannot be!', 'power' => $real_power];
            break;
        default:
            $rs = ['success' => 1, 'message' => 'default'];

    }
    return $rs;
}

/// lares_grazhdanin_horinisa
///
///
function lares_grazhdanin_horinisa($dbh, $i_user)
{
    $stage = hero_get_chars($dbh, $i_user);
    if ($stage['success'] === 0) { return $stage; }

    $stager = intval($stage['res']['stage']);
    if ($stager !== 6){
        $rs = ['success' => 0, 'Мы должны находить на уровне 6, чтобы принять задание на гражданство...'];
        return $rs;
    }
    $new_stage = 7;
    $uss = user_set_stage($dbh, $i_user, $new_stage);
    if ($uss['success'] === 0) return $uss;

//    var LaresQuest = '<span>' + 'Задание Лареса' + '</span>';
//    var LaresQuestTxt = '<li>' + '- Ларес поможет мне стать гражданином, но для этого я должен добыть для него 2 хвоста болотной крысы и 3 волчьи шкуры' + '</li>';
//    QuestListArr(LaresQuest, LaresQuestTxt, '.HaraldQuest');

    // добавлю сообщение в журнал
    $message = <<<MESSAGE
<ul class="LaresQuest">
	<li><span class="QuestTitle">Задание Лареса</span>
		<br>
		- Ларес поможет мне стать гражданином, но для этого я должен добыть для него 2 хвоста болотной крысы и 3 волчьи шкуры	
	</li>
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
    return $rs;

}

/// lares_sdat_zadanie
///
///
function lares_sdat_zadanie($dbh, $i_user)
{
    $stage = hero_get_chars($dbh, $i_user);
    if ($stage['success'] === 0) { return $stage; }

    $stager = intval($stage['res']['stage']);
    if ($stager !== 7){
        $rs = ['success' => 0, 'Мы должны находить на уровне 7, чтобы сдать задание на гражданство...'];
        return $rs;
    }

    // test
    // $rs = ['success' => 0, 'ok ok, im test it!'];
    // return $rs;

    // #1 сначала проверим, есть ли в наличии 2 хвоста и 3 шкуры...
    // #2 после вычтем столько же из инвентаря
    $lfhis = laser_find_hvosti_i_shkuri($dbh, $i_user);
    //die(Debug::d($lfhis,'$lfhis',1));
    if ($lfhis['success'] !== 1) return $lfhis;

    // hvosti i shkuri
    $nrs = ['success' => 0, 'message' => 'Не хватает хвостов и/или шкур!'];
    $hs = $lfhis['result'];
    if (count($hs) < 2) return $nrs;
    $hvosti = $hs[0];
    $shkuri = $hs[1];
    if ( intval($hvosti['i_item_type']) !== 5) {
        $hvosti = $hs[1];
        $shkuri = $hs[0];
    }
    if ( !(intval($hvosti['count']) >= 2 && intval($shkuri['count']) >= 3) ){
        return $nrs;
    }
    // выполняем теперь #2
    // #2.1
    // hvosti chisem
    $iudicbv1 = inventory_update_decItemCountByValue($dbh, 10, $i_user, 2);
    if ($iudicbv1['success'] === 0) return $iudicbv1;
    // shkuri chisem
    $iudicbv1 = inventory_update_decItemCountByValue($dbh, 11, $i_user, 3);
    if ($iudicbv1['success'] === 0) return $iudicbv1;

    // #2.2
    $iczic = inventory_clear_zeroItemCounts($dbh, $i_user);
    if ($iczic['success'] === 0) return $iczic;

    // #3 а после, если все хорошо, сменим уровень на 8, добавим сообщение в журнал и все отдадим дальше...
    $new_stage = 8;
    $uss = user_set_stage($dbh, $i_user, $new_stage);
    if ($uss['success'] === 0) return $uss;

    // добавлю сообщение в журнал
    $message = <<<MESSAGE
<ul class="LaresQuestDone">
	<li><span class="QuestTitle">Я гражданин</span>
		<br>
		- Ларес поручился за меня, я теперь гражданин Хориниса!
	</li>
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
    $rs['lares_msgs'] = 'Ларес: Ну что жe, мои поздравления, ты теперь гражданин Хориниса!';
    return $rs;
}

/// Lares - find hvosti i zhkuri
///
///
function laser_find_hvosti_i_shkuri($dbh, $i_user)
{
    $sql = <<<SQL
    SELECT
        shop_item.id,
        shop_item.name,
        shop_item.i_item_type,
        inventory.count
    FROM inventory
         LEFT JOIN shop_item on shop_item.id = inventory.i_item
         left JOIN shop on shop.id = shop_item.i_shop
    WHERE
        inventory.i_user = {$i_user} and (i_item_type = 5 or i_item_type = 6);
SQL;
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


/// Уменьшаем кол-ва итемов в инвентаре на заданное количество
///
///
function inventory_is_exists_withNeedItemCountByValue($dbh, $i_item, $i_user, $value){
    //
}

/// Уменьшаем кол-ва итемов в инвентаре на заданное количество
///
///
function inventory_update_decItemCountByValue($dbh, $i_item, $i_user, $value)
{
    //
    $sql = "UPDATE inventory SET count = count - {$value} 
        WHERE i_item = " . intval($i_item) . " and i_user = " . intval($i_user);
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен!',];
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

/// Очищаем итемы пользователя, у которых количество равно нулю
///
///
function inventory_clear_zeroItemCounts($dbh, $i_user){
    //
    $sql = "delete from inventory where i_user = {$i_user} and count = 0";
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен!',];
    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}


/// Харадь кузнец - теперь добрался и до него

///
///
///
function blacksmith_talk($dbh, $i_user)
{
    //
    $stage = hero_get_chars($dbh, $i_user);
    if ($stage['success'] === 0) { return $stage; }

    $stager = intval($stage['res']['stage']);

    switch($stager){
        case 1: case 2: case 3: case 4: case 5: case 6: case 7:
            // добавлю сообщение в журнал
            $msg_out = <<<MESSAGE
    <p>Харальд: Наша кузница производит снаряжение только для ополчения и граждан этого города! Тебя я не знаю.</p>
MESSAGE;
            $rs = ['message' => $msg_out, 'success' => 1, 'stage' => $stager];
            return $rs;
            break;

        case 8:
            $msg_out = <<<MESSAGE
<p>Говоришь нужно легендарное оружие? Изготовка оружия такого уровня это ритуал в высшем смысле этого слова, требуется особый состав для обработки стали. Добудь мне рог Мракориса! 
<button class="btn" id="questHaraldTake">Взять задание!</button> </p>
MESSAGE;
            $rs = ['message' => $msg_out, 'success' => 1, 'stage' => $stager];
            return $rs;
            break;

        case 9:

            $msg_out = "ya eshe podumau nad etim...";

            $rs = ['message' => $msg_out, 'success' => 1,'stage' => $stager];
            return $rs;
            break;
    }
}

/// blacksmith_teak_mrakkoris_quest
///
///
function blacksmith_teak_mrakkoris_quest($dbh, $i_user)
{
    //
    $stage = hero_get_chars($dbh, $i_user);
    if ($stage['success'] === 0) { return $stage; }

    $stager = intval($stage['res']['stage']);

    switch($stager){
        case 1: case 2: case 3: case 4: case 5: case 6: case 7:
        // добавлю сообщение в журнал
        $msg_out = "невозможно взять задание на столь низком уровне";
        $rs = ['message' => $msg_out, 'success' => 1];
        return $rs;
        break;

        case 8:

            $new_stage = 9;
            $uss = user_set_stage($dbh, $i_user, $new_stage);
            if ($uss['success'] === 0) return $uss;

            // добавлю сообщение в журнал
            $msg2journal = <<<MESSAGE
<ul class="HaraldQuestWeapon">
	<li><span class="QuestTitle">Оружие и броня из кузницы</span>
		<br>
		 - Харальд может изготовить мне уникальное оружие и броню. Чтобы приготовить состав для обработки стали требуется вытащить рог из опасного зверя, конечно же перед этим убив его, но как убить Мракориса?
	</li>
</ul>
MESSAGE;
            $rs = journal_add_message($dbh, $i_user, $msg2journal);
            if ($rs['success'] === 0 ) die(json_encode($rs));

            $rs = journal_get_all_messages($dbh, $i_user);
            if ($rs['success'] === 0 ) die(json_encode($rs));

            // сборка всех сообщений в 1
            $msgs = '';
            foreach($rs['result'] as $k => $v){
                $msgs .= $v['message'];
            }
            $rs['msgs'] = $msgs;
            return $rs;

        default:
            $msg_out = "невозможно взять задание на столь высоком уровне!!!";
            $rs = ['message' => $msg_out, 'success' => 1];
        return $rs;
    }
}

/// blacksmith_do_forge.php
///
///
function blacksmith_do_forge($dbh, $i_user, $i_item)
{
    // проверка, есть ли сырая сталь?
    // проверка, есть ли рог мракориса
    $igsarom = inventory_get_stalAndRogOfMrakoris($dbh, $i_user);
    if ($igsarom['success'] !== 1) return $igsarom;

    if ($igsarom['count'] !== 2) {
        $message = "Не хватает сырой стали и/или рога Мракориса!";
        return ['message' => $message, 'success' => 2];
    }

    // проверка, хватает ли денег на предмет
    $curr_gold = user_get_gold($dbh, $i_user);
    if ($curr_gold['success'] === 0){ return $curr_gold; }
    $gold = intval($curr_gold['res'][0]['gold']);

    // узнаем цену нашего предмета
    $igibi = inventory_get_itemById($dbh, $i_item);
    if ($igibi['success'] !== 1) return $igibi;
    $item_cost = intval($igibi['result']['cost']);

    if ($item_cost > $gold){
        $rs = ['success' => 2, 'message' => 'Не хватает денег на ковку предмета! :)'];
        return $rs;
    }

    // если все хорошо, отнимаем деньги, отнимаем сырую сталь и рог мракориса,
    // также добавляем выбранный для ковки предмент в инвентарь
    $new_gold = $gold - $item_cost;
    $usg = user_set_gold($dbh, $i_user, $new_gold);
    if ($usg['success'] === 0 ) return $usg;

    $uidi1 = user_inventory_del_item($dbh, 7);
    if ($uidi1['success'] === 0) return $uidi1;
    $uidi2 = user_inventory_del_item($dbh, 12);
    if ($uidi2['success'] === 0) return $uidi2;

    // добавим итем в инвентарь!
    // itemid 8 or 9
    $yiai = user_inventory_add_item($dbh, $i_item);
    if ($yiai['success'] === 0 ) return $yiai;

    $rs = ['success' => 1, 'message' => 'Предмет выкован и уже в инвентаре!', 'gold' => $new_gold];
    return $rs;
}