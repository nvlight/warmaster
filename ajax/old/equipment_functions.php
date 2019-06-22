<?php

// #########
// Equipment
// #########
function user_get_equipment($dbh, $i_user){
    //
    $sql = "SELECT 
            equipment.*,            
            shop_item.name,
            shop_item.attack,
            shop_item.armor,
            shop_item.spec_type
        FROM equipment        
        LEFT JOIN shop_item on shop_item.id = equipment.i_item
        WHERE i_user = { intval($i_user) }
         
    ";
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
function equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
{
    ///
    // set attack/gold from item type and value
    $attack = -1; $armor = -1;
    if ($item_type === 1){
        $attack = $item_value;
    }elseif ($item_type === 2){
        $armor = $item_value;
    }
    //
    $result = [];
    $ushc = user_set_hero_chars($dbh, $user_id, $attack, $armor);
    if ($ushc['success'] === 1){
        $result['message2'] = 'Характеристики героя успешно заданы!';
        $result['success2'] = 1;
        //
        if ($item_type === 1){
            $result['attack'] = $item_value;
        }elseif ($item_type === 2){
            $result['armor'] = $item_value;
        }
    }else{
        $result['success2'] = 0;
    }

    return $result;
}

////////
function user_equipment_do_old_1($dbh, $user_id, $i_item)
{
    //
    $get_item = user_get_shopitem_by_id($dbh, $i_item);
    //echo Debug::d($get_item);
    if ($get_item['success'] === 0) {
        return $get_item;
    }
    $item_name = $get_item['result'][0]['name'];

    //
    $item = $get_item['result'][0];
    $shop_item_id = $item['id'];
    $get_item_type = user_get_item_type($item)['item_type'];
    $get_item_value = user_get_item_type($item)['item_value'];
    //echo Debug::d($get_item_type,'',2);
    if ($get_item_type === 0) {
        return ['success' => 0, 'message' => 'item type - armor/attack/spec is undefined'];
    }
    $item['item_type'] = $get_item_type;
    //echo Debug::d($item);

    // если тип итема не из (1,2) attack/armor
    // это тоже надо где-то да проверить...
    if ($item['item_type'] !== 1 && $item['item_type'] !== 2){
        return ['success' => 2, 'message' => 'невозможно экиппировать!'];
    }


    // теперь важный момент, мы просматриваем все экиппированные итемы героя
    // если тип, который мы добавляем уже есть, мы просто должны обновить item_id экиппированного итема
    // иначе мы должны добавить этот новый итем с новым типом урон-атака-спец_тип
    //
    $gue = user_get_equipment($dbh, $user_id);
    //echo Debug::d($gue,'user_get_equipment',1);
    if ($gue['success'] === 0){
        return $gue;
    }
    // значит герой не экипирован вообще!
    if ($gue['success'] === 2) {
        // well, we need to insert new value
        $do_add_equipment = user_add_equipment($dbh, $user_id, $i_item);
        $do_add_equipment['item_type'] = $item['item_type'];
        $do_add_equipment['item_value'] = $get_item_value;

        // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
        $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $do_add_equipment['item_type'], $do_add_equipment['item_value']);
        foreach(array_keys($rtmp) as $k => $v){
            $do_add_equipment[$v] = $rtmp[$v];
        }
        $do_add_equipment['i_item'] = $shop_item_id;
        $do_add_equipment['item_name'] = $item_name;
        return $do_add_equipment;
    }

    // теперь герой экиппирован, если найден итем с таким же типом обновляем, иначего просто добавляем

    // добавляем item_type ко всем
    foreach($gue['result'] as $k => $v)
    {
        $get_type = user_get_item_type($v)['item_type'];
        $gue['result'][$k]['item_type'] = $get_type;
    }
    //echo Debug::d($gue['result'],'gue_results_with_item_type');

    // # 2
    foreach($gue['result'] as $k => $v){
        if ($v['item_type'] === $item['item_type']){
            $i_item_old = $v['i_item'];
            $i_item_new = $item['id'];
//            echo 'need to update!';
//            echo Debug::d('$i_item_old: ' . $i_item_old, 'i_item_old');
//            echo Debug::d('$i_item_new: ' . $i_item_new, 'i_item_new');
            $rss = user_update_equipment($dbh,$user_id, $i_item_old, $i_item_new);
            $rss['item_type'] = $item['item_type'];
            $rss['item_value'] = $get_item_value;

            // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
            $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $rss['item_type'], $rss['item_value']);
            foreach(array_keys($rtmp) as $k => $v){
                $rss[$v] = $rtmp[$v];
            }

            $rss['i_item'] = $shop_item_id;
            $rss['item_name'] = $item_name;
            return $rss;
        }
    }

    // если до этого места без ошибок и без ретурнов, значит, это другой тип, и его нужно добавить в БД
    $do_add_equipment = user_add_equipment($dbh, $user_id, $i_item);
    $do_add_equipment['message'] = 'Запрос выполнен, герой принял начальную экиппировку, тип другой уже';
    $do_add_equipment['success'] = 5;
    $do_add_equipment['item_type'] = $item['item_type'];
    $do_add_equipment['item_value'] = $get_item_value;

    // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
    $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $do_add_equipment['item_type'], $do_add_equipment['item_value']);
    foreach(array_keys($rtmp) as $k => $v){
        $do_add_equipment[$v] = $rtmp[$v];
    }
    $do_add_equipment['i_item'] = $shop_item_id;
    $do_add_equipment['item_name'] = $item_name;
    return $do_add_equipment;
}

// what is the item type - Attack | Armor | Special type = 1 | 2 | 3
function user_get_item_type($item_arr)
{
    $item_type = 0; // its mean is error.
    //(
    //   [id] => 39
    //   [i_user] => 8
    //   [i_item] => 5
    //   [name] => Кожаная броня
    //   [attack] => 0
    //   [armor] => 5
    //   [spec_type] =
    //)
    $p1 = $item_arr['attack'] * 1;
    $p2 = $item_arr['armor'] * 1;
    //echo Debug::d($item_arr,'search_for_i_item!');

    if ($p1 > 0) { $item_type = 1; $item_value = $p1; }
    elseif ($p2 > 0) { $item_type = 2; $item_value = $p2; }
    elseif ($p1 === 0 && $p2 === 0) { $item_type = 3; $item_value = 0; }

    return ['item_type' => $item_type, 'item_value' => $item_value,
        //'i_item' => $item_arr['i_item']
    ];
}

//
function user_add_equipment($dbh, $i_user, $i_item )
{
    $sql = $dbh->prepare('INSERT INTO equipment (i_user, i_item) VALUES (?,?)' );
    try{
        //$rs = $sql->execute(['ivan','iPaa@@Sss1', 'ivi@gmail.com']);
        $rs = $sql->execute([$i_user, $i_item]);
        $rs = [
            'success' => 5,
            'message' => 'Запрос выполнен, герой принял начальную экиппировку',
        ];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function user_update_equipment($dbh, $i_user, $i_item_old, $i_item_new )
{
    //$sql = $dbh->prepare('INSERT INTO equipment (i_user, i_item) VALUES (?,?)' );
    $sql = "UPDATE equipment SET i_item = {$i_item_new} WHERE i_item = {$i_item_old} and i_user = {$i_user}";
    try{
        $dbh->exec($sql);
        $rs = [
            'success' => 4,
            'message' => 'Запрос выполнен, эккипировка обновлена',
        ];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка. Попробуйте позднее.'
        ];
    }
    return $rs;
}

////////
function user_equipment_do($dbh, $user_id, $i_item)
{
    //
    $get_item = user_get_shopitem_by_id($dbh, $i_item);
    //echo Debug::d($get_item);
    if ($get_item['success'] === 0) {
        return $get_item;
    }
    $item_name = $get_item['result'][0]['name'];

    //
    $item = $get_item['result'][0];
    $shop_item_id = $item['id'];
    //$get_item_type = user_get_item_type($item)['item_type'];
    //$get_item_value = user_get_item_type($item)['item_value'];
    $get_item_type = intval($get_item['result'][0]['i_item_type']);
    $get_item_value = $get_item['result'][0]['value'];
    //echo Debug::d($get_item_type,'',2);
    if ($get_item_type === 0) {
        return ['success' => 0, 'message' => 'item type - armor/attack/spec is undefined'];
    }
    $item['item_type'] = $get_item_type;
    //echo Debug::d($item);
    //echo Debug::d($get_item_type,'',2);

    // если тип итема не из (1,2) attack/armor
    // это тоже надо где-то да проверить...
    if ($get_item_type !== 1 && $get_item_type !== 2){
        return ['success' => 2, 'message' => 'невозможно экиппировать!', '$get_item_type' => $get_item_type];
    }


    // теперь важный момент, мы просматриваем все экиппированные итемы героя
    // если тип, который мы добавляем уже есть, мы просто должны обновить item_id экиппированного итема
    // иначе мы должны добавить этот новый итем с новым типом урон-атака-спец_тип
    //
    $gue = user_get_equipment($dbh, $user_id);
    //echo Debug::d($gue,'user_get_equipment',1);
    if ($gue['success'] === 0){
        return $gue;
    }
    // значит герой не экипирован вообще!
    if ($gue['success'] === 2) {
        // well, we need to insert new value
        $do_add_equipment = user_add_equipment($dbh, $user_id, $i_item);
        $do_add_equipment['item_type'] = $item['item_type'];
        $do_add_equipment['item_value'] = $get_item_value;

        // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
        $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $do_add_equipment['item_type'], $do_add_equipment['item_value']);
        foreach(array_keys($rtmp) as $k => $v){
            $do_add_equipment[$v] = $rtmp[$v];
        }
        $do_add_equipment['i_item'] = $shop_item_id;
        $do_add_equipment['item_name'] = $item_name;
        return $do_add_equipment;
    }

    // теперь герой экиппирован, если найден итем с таким же типом обновляем, иначего просто добавляем

    // добавляем item_type ко всем
    foreach($gue['result'] as $k => $v)
    {
        //$get_type = user_get_item_type($v)['item_type'];
        $gue['result'][$k]['item_type'] = $v['i_item_type'];
    }
    //echo Debug::d($gue['result'],'gue_results_with_item_type');

    // # 2
    foreach($gue['result'] as $k => $v){
        if ($v['item_type'] === $item['item_type']){
            $i_item_old = $v['i_item'];
            $i_item_new = $item['id'];
//            echo 'need to update!';
//            echo Debug::d('$i_item_old: ' . $i_item_old, 'i_item_old');
//            echo Debug::d('$i_item_new: ' . $i_item_new, 'i_item_new');
            $rss = user_update_equipment($dbh,$user_id, $i_item_old, $i_item_new);
            $rss['item_type'] = $item['item_type'];
            $rss['item_value'] = $get_item_value;

            // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
            $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $rss['item_type'], $rss['item_value']);
            foreach(array_keys($rtmp) as $k => $v){
                $rss[$v] = $rtmp[$v];
            }

            $rss['i_item'] = $shop_item_id;
            $rss['item_name'] = $item_name;
            return $rss;
        }
    }

    // если до этого места без ошибок и без ретурнов, значит, это другой тип, и его нужно добавить в БД
    $do_add_equipment = user_add_equipment($dbh, $user_id, $i_item);
    $do_add_equipment['message'] = 'Запрос выполнен, герой принял начальную экиппировку, тип другой уже';
    $do_add_equipment['success'] = 5;
    $do_add_equipment['item_type'] = $item['item_type'];
    $do_add_equipment['item_value'] = $get_item_value;

    // equipment_set_attackAndArmor($dbh, $user_id, $item_type, $item_value)
    $rtmp = equipment_set_attackAndArmor($dbh, $user_id, $do_add_equipment['item_type'], $do_add_equipment['item_value']);
    foreach(array_keys($rtmp) as $k => $v){
        $do_add_equipment[$v] = $rtmp[$v];
    }
    $do_add_equipment['i_item'] = $shop_item_id;
    $do_add_equipment['item_name'] = $item_name;
    return $do_add_equipment;
}