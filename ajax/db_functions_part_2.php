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




///