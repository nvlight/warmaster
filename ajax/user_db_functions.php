<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 23:30
 */

// check all params
function check_params($need_form_keys, $additional_form_keys)
{

    $sak = array_keys($_POST);
    //echo Debug::d($sak);

    // check for needed form keys
    $all_keys_is_exists = true;
    foreach($need_form_keys as $k => $v){

        if (!in_array($v[1], $sak, true)) {
            $all_keys_is_exists = false;
            break;
        }
    }

    if (!$all_keys_is_exists){
        $rs = ['success' => 0, 'message' => 'Заполните все поля!' , 'data' => ''];
        die(json_encode($rs));
    }

    // check form values from pattern
    $all_checks_fine = true;
    $last_key_unchecked = '';
    $last_key_error_message = '';
    foreach($need_form_keys as $k => $v){
        $pattern = "#".$v[2]."#u";
        if (!preg_match($pattern, $_POST[$v[1]])){
            $all_checks_fine = false;
            $last_key_unchecked = $v[1];
            $last_key_error_message = $v[3];
            break;
        }
    }
    if (!$all_checks_fine){
        $rs = ['success' => 0, 'message' => $last_key_error_message , 'last_error_key' => $last_key_unchecked];
        die(json_encode($rs));
    }

    return true;
}

// тут нужно добавить функцию, которая узнает, занят ли текущий емайл
function is_email_duplicate($mysql, $useremail){

    $sql = $mysql['connect']->prepare('SELECT LCASE(`mail`) FROM user WHERE LCASE(mail) = LCASE(?)' );
    try{
        $rs = $sql->execute([$useremail]);
        $rs_count = count($sql->fetchAll(MYSQLI_NUM));
        //echo Debug::d($rs_count,'',2);

        if ($rs_count === 1){
            $rs = [
                'success' => 0,
                'message' => 'Данный email занят!',
            ];
        }else{
            $rs = [
                'success' => 1,
                'message' => 'Данный email свободен!',
            ];
        }


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
function mySendMailMessage($subject, $msg_header, $need_form_keys, $additional_form_keys)
{
    $myParams = require '../config/params.php'; $params = $myParams;
    $myConfig = require '../config/swift_mailer_config.php';
    $myParams['sw_subject'] = $subject;

    try {
        // Create the SMTP Transport
        $transport = (new Swift_SmtpTransport($myConfig['mailer']['transport']['host'],
            $myConfig['mailer']['transport']['port']))
            ->setUsername($myConfig['mailer']['transport']['username'])
            ->setPassword($myConfig['mailer']['transport']['password'])
            ->setEncryption($myParams['sw_enc']);

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = new Swift_Message();

        // Set a "subject"
        $message->setSubject($myParams['sw_subject']);

        // Set the "From address"
        $message->setFrom([$myParams['sw_frommail'] => $myParams['my_name']]);

        // Set the "To address" [Use setTo method for multiple recipients, argument should be array]
        $message->addTo( $myParams['sw_tomail2'],'recipient name');

        // Add "CC" address [Use setCc method for multiple recipients, argument should be array]
        //$message->addCc('recipient@gmail.com', 'recipient name');

        // Add "BCC" address [Use setBcc method for multiple recipients, argument should be array]
        //$message->addBcc('recipient@gmail.com', 'recipient name');

        // Add an "Attachment" (Also, the dynamic data can be attached)
        //$attachment = Swift_Attachment::fromPath('example.xls');
        //$attachment->setFilename('report.xls');
        //$message->attach($attachment);

        // Add inline "Image"
        //$inline_attachment = Swift_Image::fromPath('nature.jpg');
        //$cid = $message->embed($inline_attachment);

        // Set the plain-text "Body"
        //$message->setBody("This is the plain text body of the message.\nThanks,\nAdmin");

        $message->addPart($msg_header, 'text/html');

        foreach($need_form_keys as $k => $v){
            $clear_val = Debug::encode($_POST[$v[1]]);
            $message->addPart($v[0] . ': ' . $clear_val, 'text/html');
        }
        foreach($additional_form_keys as $k => $v){
            if (array_key_exists($v[1], $_POST)) {
                $clear_val = Debug::encode($_POST[$v[1]]);
                $message->addPart($v[0] . ': ' . $clear_val, 'text/html');
            }
        }

        // Send the message
        $result = $mailer->send($message);
        $rs2 = ['success' => 0, 'message' => 'we send the message!',
            'add_info' => $result,
        ];

        //die(json_encode($rs2));
    } catch (Exception $e) {
        $rs2 = ['success' => 0, 'message' => $e->getMessage() ];

        //die(json_encode($rs2));
    }
}

//
function add_new_warmaster_user($mysql, $user_data, $need_form_keys, $additional_form_keys, $subject, $msg_header)
{
    $sql = $mysql->prepare('INSERT INTO `user` (username, userpassword, mail, i_group) VALUES (?,?,?,?)' );
    try{
        //$rs = $sql->execute(['ivan','iPaa@@Sss1', 'ivi@gmail.com']);
        $rs = $sql->execute($user_data);
        $rs = [
            'success' => 1,
            'message' => 'Пользователь зарегистрирован!',
        ];

        // сюда же сразу ложим отправку сообщения на мейл!
        mySendMailMessage($subject, $msg_header, $need_form_keys, $additional_form_keys);

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

//
function get_user_by_mail($dbh, $mail)
{
    //
    $sql = "SELECT id, username, mail FROM user WHERE mail = '{$mail}'";
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, ресурсы найдены!',
                'res' => $sql_rs2
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

//
function set_startup_resourses($dbh, $user_mail)
{
    //
    $user = get_user_by_mail($dbh, $user_mail);
    if ($user['success'] === 1){
        //
        $user = $user['res'][0];

        $startup_user_res = [];
        // 'gold', 'health', 'armour_count', 'critical', 'power', 'damage', 'weapon', 'armour_item'
        $startup_user_res['id'] = $user['id'];
        $startup_user_res['username']     = $user['username'];
        $startup_user_res['userpassword'] = $user['userpassword'];
        $startup_user_res['mail']         = $user['mail'];

        $startup_user_res['gold'] = 500;
        $startup_user_res['health'] = 100;
        $startup_user_res['armour_count'] = 0;
        $startup_user_res['armour_item'] = null;
        $startup_user_res['critical'] = 20; // 20%
        $startup_user_res['power'] = 0;
        $startup_user_res['damage'] = 10;
        $startup_user_res['weapon'] = null;

        $startup_user_res['stage'] = 0;
        $res_st = $startup_user_res;
        $res_st_json = json_encode($res_st);

        $upd = update_user_resourses($dbh, $user['id'], $res_st_json);
        if ($upd['success'] === 1){
            $rs = ['success' => 1, 'message' => 'Стартовые ресурсы установлены' ];
        }else{
            $rs = ['success' => 0, 'message' => 'Стартовые ресурсы НЕ установлены' ];
        }
    }else{
        $rs = ['success' => 0, 'message' => 'Пользователь не найден' ];
    }

    return $rs;
}

// ищем пользователя, по логину и паролю
function login($mysql, $mail, $userpassword){
    $dbh = $mysql['connect'];

    $sql = $dbh->prepare('SELECT 
            user.id, user.username, user.mail,
            hi.armor, hi.critical, hi.gold, hi.health, hi.power, hi.stage, hi.attack
        FROM user 
        LEFT JOIN `hero_info` hi on hi.i_user = user.id      
        WHERE mail = ? and userpassword = ?'
    );
    try{
        $rs = $sql->execute([$mail, $userpassword]);
        $rs_sql = ($sql->fetchAll(MYSQLI_NUM));
        //echo Debug::d($rs_sql,'',2);

        if (count($rs_sql) === 0){
            $rs = [
                'success' => 0,
                'message' => 'Неверный логин и/или пароль!',
            ];
        }else{
            $rs = ['success' => 1, 'message' => 'Авторизовались!', 'rs' => $rs_sql[0], ];
        }
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
function get_hero_chars($dbh, $user_id)
{
    //
    $sql = "SELECT * FROM hero_info WHERE i_user = " . intval($user_id);
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

//
function zhournal_set($dbh, $user_id, $zhournal){
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, журнал НЕ получен!',];
    $sql = "UPDATE user SET zhournal = $zhournal WHERE id = " . intval($user_id);
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, журнал получен!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function zhournal_get($dbh, $user_id){

    //
    $sql = "SELECT zhournal FROM user WHERE id = " . intval($user_id);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, ресурсы найдены!',
                'res' => $sql_rs2
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

//
function user_set_res_by_key($key){

}

//
function user_set_startup_chars($dbh, $user_id)
{
    //
    // $rs = ['success' => 0, 'message' => 'Запрос выполнен, стартовые характеристики героя НЕ заданы!',];
    $sql = "INSERT INTO hero_info VALUE(NULL,$user_id,700,0,100,0,20,0)";
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, стартовые характеристики героя заданы!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function user_set_stage($dbh, $user_id, $stage=0)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, уровень НЕ обновлен!',];
    $sql = "UPDATE hero_info SET stage = $stage WHERE i_user = " . intval($user_id);
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, уровень обновлен!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function user_get_stage($dbh, $user_id=0)
{
    //
    $sql = "SELECT hi.stage FROM user Left Join hero_info hi on hi.i_user = user.id WHERE user.id = " . intval($user_id);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, уровень найден!',
                'res' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 0,
                'message' => 'Запрос выполнен, уровень Не найден!',
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

//
function user_set_gold($dbh, $user_id, $gold=0)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, золото НЕ установлено!',];
    $sql = "UPDATE hero_info SET gold = $gold WHERE i_user = " . intval($user_id);
    //echo $sql;
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, золото установлено!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function user_get_gold($dbh, $user_id=0)
{
    //
    $sql = "SELECT gold FROM hero_info WHERE i_user = " . intval($user_id);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, золото найдено!',
                'res' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 0,
                'message' => 'Запрос выполнен, золото НЕ найдено!',
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

//
function user_set_health($dbh, $user_id, $health)
{
    //
    $rs = ['success' => 0, 'message' => 'Запрос выполнен, золото НЕ установлено!',];
    $sql = "UPDATE hero_info SET health = $health WHERE i_user = " . intval($user_id);
    //echo $sql;
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, золото установлено!',];

    }catch (Exception $e){
        $rs = [
            'success' => 0,
            'message2' => $e->getMessage() . ' : ' . $e->getCode(),
            'message' => 'Ошибка при запросе. Попробуйте позднее.'
        ];
    }
    return $rs;
}

//
function user_get_health($dbh, $user_id=0)
{
    //
    $sql = "SELECT health FROM hero_info WHERE i_user = " . intval($user_id);
    try{
        $sql_rs1  = $dbh->query($sql);
        $sql_rs2 = ($sql_rs1->fetchAll(MYSQLI_NUM));
        //echo Debug::d($sql);
        //echo Debug::d($sql_rs1,'',2);
        //echo Debug::d($sql_rs2,'',2);
        if (count($sql_rs2)){
            $rs = [
                'success' => 1,
                'message' => 'Запрос выполнен, найдено!',
                'res' => $sql_rs2
            ];
        }else{
            $rs = [
                'success' => 0,
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