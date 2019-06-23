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
function user_set_startup_chars($dbh, $user_id)
{
    //
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
function user_set_hero_chars($dbh, $user_id, $type=0, $value=0)
{
    //
    $sql = "
        UPDATE hero_info SET 
            {$type} = {$value}            
        WHERE i_user = {$user_id}";
    //
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен, характеристики героя обновлены!',];

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
        $rs = ['success' => 5, 'message' => 'Запрос выполнен, золото установлено!', 'gold' => $gold];

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
    $rs = ['success' => 0, 'message' => 'Запрос выполнен!',];
    $sql = "UPDATE hero_info SET health = $health WHERE i_user = " . intval($user_id);
    //echo $sql;
    try{
        $dbh->exec($sql);
        $rs = ['success' => 1, 'message' => 'Запрос выполнен!', 'health' => $health];

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

//
function user_get_shops_with_childs($dbh)
{
    //
    $sql = "
        SELECT
            shi.id i_item,
            shop.id i_shop,
            shop.name shop_name,
            shi.name item_name,
            shi.value item_value,
            shi.cost,
            shi.i_item_type,
            shop_item_type.name item_type_name
        FROM
             shop_item shi
        LEFT JOIN shop ON shop.id = shi.i_shop
        LEFT join shop_item_type on shop_item_type.id = shi.i_item_type
        LIMIT 100";
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
                'result' => $sql_rs2
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

// далее нужны 2 функции для добавления и удаления предметов героя
// можно пойти двумя путями.
// 1. заносить каждый раз новую запись по ИД итема и также удалять по 1 разу. Реализация кажется легкой
// 2. занести под отдельный итем ИД конкретный и увеличивать счетчик. И уменьшать 1 если счетчик больше 1.
// 2.1 есть ли итем с заданным ИД?
// 2.2 если итем есть и счетчик больше 1 уменьшить счетчик на 1
// 2.3 если счетчик = 1 удалить итем.

//
function user_inventory_is_item_exists($dbh, $i_item, $i_user)
{
//
    $sql = "SELECT count FROM inventory WHERE i_item = " . intval($i_item) . " and i_user = " . intval($i_user);

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
                'result' => $sql_rs2
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

//
function user_inventory_add_item($dbh, $i_item)
{
    //
    $new_count = 1;
    $i_user = $_SESSION['user']['id'];
    $is_item_exists = user_inventory_is_item_exists($dbh, $i_item, $i_user);
    //echo Debug::d($is_item_exists,'',2); //die;
    //echo Debug::d(count($is_item_exists['result']));
    if ($is_item_exists['success'] === 1 && count($is_item_exists['result']) ){
        $new_count =  intval($is_item_exists['result'][0]['count']);
        $new_count++;
        return user_inventory_update_item($dbh, $i_item, $i_user, $new_count);
    }

    $sql = $dbh->prepare("INSERT INTO inventory(i_user, i_item, count) VALUES (?, ?, $new_count)");
    try{
        $str = $sql->execute([$_SESSION['user']['id'], $i_item]);
        $rs = [
            'success' => 1,
            'message' => 'Запись добавлена!',
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
function user_inventory_del_item($dbh, $i_item)
{
    //
    $i_user = $_SESSION['user']['id'];
    $is_item_exists = user_inventory_is_item_exists($dbh, $i_item, $i_user);
    //echo Debug::d($is_item_exists,'',2); //die;
    //echo Debug::d(count($is_item_exists['result']));
    if ($is_item_exists['success'] === 1 && (intval($is_item_exists['result'][0]['count']) - 1) > 0 ){
        $new_count =  intval($is_item_exists['result'][0]['count']);
        $new_count--;
        //echo Debug::d($new_count,'new_count');
        return user_inventory_update_item($dbh, $i_item, $i_user, $new_count);
    }
    //
    $sql = "DELETE FROM inventory WHERE i_item = " . intval($i_item) . " and i_user = " . $_SESSION['user']['id'];
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

//
function user_inventory_update_item($dbh, $i_item, $i_user, $new_count)
{
    //
    $sql = "UPDATE inventory SET count = $new_count WHERE i_item = " . intval($i_item) . " and i_user = " . intval($i_user);
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

// получение итема под ИД
function user_get_shopitem_by_id($dbh, $i_item)
{
    //
    $sql = "SELECT * FROM shop_item WHERE id = " . intval($i_item);
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

//
function user_inventory_get_all_childs($dbh, $i_user)
{
    //
    $sql = "
        SELECT                        
            shop.name shop_name,
            inventory.id inv_id,
            inventory.count inv_count,            
            shop_item.id item_id,
            shop_item.name item_name,
            shop_item.i_item_type item_type,
            shop_item.value,            
            shop_item.cost                        
        FROM
            inventory
        LEFT JOIN shop_item ON inventory.i_item = shop_item.id
        LEFT JOIN shop ON shop_item.i_shop = shop.id
        WHERE inventory.i_user = { intval($i_user) }
        LIMIT 100";
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

//
function user_inventory_get_all_childs_html($result_array)
{
    $result_str = '';
    //echo Debug::d($result_array); die;
    if ( ($result_array !== null) && is_array($result_array) && count($result_array) )
    {
        //
        foreach($result_array as $item_key => $item_value)
        {
            //
            $tmp_str = <<<TMP_STR
            <li>
                <label>
                    <input class="inp_radio" name="inventory" type="radio" data-itemid="{$item_value['item_id']}" >
                        <span class="itemName">{$item_value['item_name']}</span>
                        <span class="counter counter-0 ">{$item_value['inv_count']},</span>
                        <span class="priceItemHero">{$item_value['cost']},</span>
                        <span class="damageItemHero">{$item_value['value']}</span>
                    </input>
                </label>
            </li>
TMP_STR;
            $result_str .= $tmp_str;
        }
    }

    return ['success' => 1 , 'result' => $result_str];
}

//
//// ### получаем инвентарь пользователя...
function user_inventory_get($dbh)
{
    //
    $inv_childs = user_inventory_get_all_childs($dbh, $_SESSION['user']['id']);
    if ($inv_childs['success'] !== 0){
        $WM_user_inventory = $inv_childs['result'];
        $WM_user_inventory = user_inventory_get_all_childs_html($WM_user_inventory);
        //echo Debug::d($WM_user_inventory,'woow',1);
        return $WM_user_inventory;
    }
    return $inv_childs;
}

//
function inventory_get_item_by_id($dbh, $i_user, $_item){

}

//
function user_inventory_buy_item($dbh, $user_id, $i_item)
{
    /// ####
    ///
    //echo Debug::d($_SESSION);
    $curr_shop_item['id'] = $i_item;
    $curr_shop_item['inner'] = user_get_shopitem_by_id($dbh, $curr_shop_item['id']);
    //echo Debug::d($curr_shop_item); die;
    if ($curr_shop_item['inner']['success'] === 0) {
        return $curr_shop_item['inner'];
    }

    $curr_shop_item['price'] = $curr_shop_item['inner']['result'][0]['cost'] * 1;
    //echo Debug::d($curr_shop_item['price']);

    // # 1 new ---> test user_add_item
    // добавление и обновление итемов по ИД работает!
    // сначала проверим, хватает ли денег на покупку текущего итема.
    $user['curr_gold'] = user_get_gold($dbh, $user_id);
    if ($user['curr_gold']['success'] === 1)
    {
        $cu_gold = $user['curr_gold']['res'][0]['gold'] * 1;
        $nu_gold = $cu_gold - $curr_shop_item['price'];
        //echo Debug::d($cu_gold,'$cu_gold',2);
        //echo Debug::d($nu_gold,'$nu_gold',2);
        if ($nu_gold < 0){
            // more gold required !
            return ['success' => 6, 'message' => '<p>' . 'Торговец: Эта вещь тебе явно не по карману :)' . '</p>'];
        }
        //
        $ruait = user_inventory_add_item($dbh, $curr_shop_item['id']);
        //echo Debug::d($ruait,'');

        if ($ruait['success'] === 1){
            //echo Debug::d($user['curr_gold'],'',1);
            $usg = user_set_gold($dbh, $user_id, $nu_gold);
            //echo Debug::d($usg,'',2);
            return $usg;
        }
        return $ruait;

    }

    return $user['curr_gold'];

    // # 2 new ---> test user_del_item_by_ID
    // удаление и обновление итемов по ИД тоже работает!
    //$ruaig = user_inventory_del_item($dbh, 5);
    //die;
}

//
function user_inventory_sell_item($dbh, $user_id, $i_item)
{
    /// ####
    //echo Debug::d($_SESSION);
    $curr_shop_item['id'] = $i_item;
    $curr_shop_item['inner'] = user_get_shopitem_by_id($dbh, $curr_shop_item['id']);
    //echo Debug::d($curr_shop_item); die;
    if ($curr_shop_item['inner']['success'] === 0) {
        return $curr_shop_item['inner'];
    }

    // current item price --->
    $curr_shop_item['price'] = $curr_shop_item['inner']['result'][0]['cost'] * 1;
    //echo Debug::d($curr_shop_item['price']);

    // # 1 new ---> test user_add_item
    $user['curr_gold'] = user_get_gold($dbh, $user_id);
    if ($user['curr_gold']['success'] === 1) {
        $cu_gold = $user['curr_gold']['res'][0]['gold'] * 1;
        // # т.к. мы продаем за пол-цены, усекаем на половину голду.
        $curr_shop_item['price'] = intval($curr_shop_item['price'] / 2);
        $nu_gold = $cu_gold + $curr_shop_item['price'];
    }else{
        return $user['curr_gold'];
    }

    $ruait = user_inventory_del_item($dbh, $curr_shop_item['id']);
    //echo Debug::d($ruait,'');

    if ($ruait['success'] === 1) {
        //echo Debug::d($user['curr_gold'],'',1);
        $usg = user_set_gold($dbh, $user_id, $nu_gold);
        //echo Debug::d($usg,'',2);
        return $usg;
    }

    return $ruait;
}


// #########
// Equipment
// #########
function user_get_equipment($dbh, $i_user){
    //
    $sql = "SELECT 
            equipment.*,            
            shop_item.name,
            shop_item.value,
            shop_item.i_item_type            
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
function user_equipment_do($dbh, $user_id, $i_item)
{
    // получаем сразу же эпипировку и данные текущего итема по ИД
    $gue = user_get_equipment($dbh, $user_id);
    //echo Debug::d($gue,'user_get_equipment',1);
    //
    $get_item = user_get_shopitem_by_id($dbh, $i_item);
    //echo Debug::d($get_item,'user_get_shop_item_by_id: ' . $i_item,1); //die;

    // сразу 2 проверки на 2 предыдущих запроса
    if ($gue === 0){
        return $gue;
    }
    if ($get_item['success'] === 0) {
        return $get_item;
    }

    // тут должна быть важная проверка, если предмет не оружие и не броня, сразу же выходим!
    if ($get_item['result'][0]['i_item_type'] != 1 && $get_item['result'][0]['i_item_type'] != 2 ){
        return ['success' => 2, 'message' => 'Невозножно экиппировать!'];
    }

    // если экипировка героя пустая, сразу же добавляем текущий предмет...
    if ($gue['success'] === 2)
    {
        //
        $uae = user_add_equipment($dbh, $user_id, $i_item);
        if ($uae['success'] === 0){
            return $uae;
        }
        //echo Debug::d($uae);
        //return $uae;
        $curr_item = $get_item['result'][0];
        // user_set_hero_chars($dbh, $user_id, $type=0, $value=0)
        if ( intval($curr_item['i_item_type']) === 1){
            $type = "attack";
        }else{
            $type = "armor";
        }
        $ushc = user_set_hero_chars($dbh, $user_id, $type, $curr_item['value']);
        $ushc['item_type']  = $curr_item['i_item_type'];
        $ushc['item_name']  = $curr_item['name'];
        $ushc['item_value'] = $curr_item['value'];
        $ushc['i_item']     = $curr_item['id'];
        return $ushc;

    }elseif($gue['success'] === 1){
        //
        // теперь возможны несколько вариантов, если i_item_type того что в базе и того что на входе совпадают
        // то мы должны лишь обновить текущий итем
        $equip = $gue['result'][0];
        $curr_item = $get_item['result'][0];
        $i_item_old = $equip['i_item'];
        $i_item_new = $curr_item['id'];
        if ($equip['i_item_type'] === $curr_item['i_item_type']) {
            $uue = user_update_equipment($dbh,$user_id, $i_item_old, $i_item_new);
            if ($uue['success'] === 0){
                return $uue;
            }
            //echo Debug::d($uue);
            //return $uue;
            // user_set_hero_chars($dbh, $user_id, $type=0, $value=0)
            if ( intval($curr_item['i_item_type']) === 1){
                $type = "attack";
            }else{
                $type = "armor";
            }
            $ushc = user_set_hero_chars($dbh, $user_id, $type, $curr_item['value']);
            $ushc['item_type']  = $curr_item['i_item_type'];
            $ushc['item_name']  = $curr_item['name'];
            $ushc['item_value'] = $curr_item['value'];
            $ushc['i_item']     = $curr_item['id'];
            return $ushc;
        }else{
            // перед нами другой тип, противоположный текущему
            // если предмет такого же типа уже есть в экиппировках, нужно только обновить иначе добавляем как новый
            $is_item_new = false;
            $all_equip = $gue['result'];
            foreach($all_equip as $k => $v){
                if ( $curr_item['i_item_type'] === $v['i_item_type'] ){
                    $is_item_new = true;
                    $i_item_old = $v['i_item'];
                    break;
                }
            };

            //
            if ($is_item_new){
                $uue = user_update_equipment($dbh,$user_id, $i_item_old, $i_item_new);
                if ($uue['success'] === 0){
                    return $uue;
                }
                //echo Debug::d($i_item_old . ' : ' . $i_item_old);
                //echo Debug::d($uue);
                //return $uue;
                // user_set_hero_chars($dbh, $user_id, $type=0, $value=0)
                if ( intval($curr_item['i_item_type']) === 1){
                    $type = "attack";
                }else{
                    $type = "armor";
                }
                $ushc = user_set_hero_chars($dbh, $user_id, $type, $curr_item['value']);
                $ushc['item_type']  = $curr_item['i_item_type'];
                $ushc['item_name']  = $curr_item['name'];
                $ushc['item_value'] = $curr_item['value'];
                $ushc['i_item']     = $curr_item['id'];
                return $ushc;
            }else{
                $uae = user_add_equipment($dbh, $user_id, $i_item);
                if ($uae['success'] === 0){
                    return $uae;
                }
                //echo Debug::d($uae);
                //return $uae;
                // user_set_hero_chars($dbh, $user_id, $type=0, $value=0)
                if ( intval($curr_item['i_item_type']) === 1){
                    $type = "attack";
                }else{
                    $type = "armor";
                }
                $ushc = user_set_hero_chars($dbh, $user_id, $type, $curr_item['value']);
                $ushc['item_type']  = $curr_item['i_item_type'];
                $ushc['item_name']  = $curr_item['name'];
                $ushc['item_value'] = $curr_item['value'];
                $ushc['i_item']     = $curr_item['id'];
                return $ushc;
            }

        }
    }
    //die('weAw!');
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
