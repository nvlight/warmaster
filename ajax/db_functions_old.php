<?php

// old funciton - for delete
function update_user_resourses($dbh, $user_id, $res)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, ресурсы НЕ обновлены!',];
    $sql = "UPDATE user SET resourses = '$res' WHERE id = $user_id";
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, ресурсы обновлены!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}