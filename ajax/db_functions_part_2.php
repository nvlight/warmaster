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
                $message = '<p>' . 'Сентеза: Такой разговор мне по душе, можешь проходить :)' . '</p>';

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
                    return ['success' => 1, 'message' => $message, 'gold' => $new_gold];
                }
                // -100 gold
                // hero_gold_set_withDec($dbh, $i_user, $dec_value)
                return ['success' => 1, 'message' => $message];
            } elseif ($choise === 2){
                $message = '<p>' . "Сентеза избил тебя и забрал все деньги!" . '</p>';

                // set gold to zero (0)
                $usg = user_set_gold($dbh, $i_user, 0);
                if ($usg['success'] === 0 ){ return $usg;}
                //
                $uss = user_set_stage($dbh, $i_user, 3);
                if ($uss['success'] === 0 ){ return $uss;}

                return ['success' => 1, 'message' => $message, 'gold' => 0];
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