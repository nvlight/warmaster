<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $rs = ['success' => 0, 'message' => 'There is no POST!'];
    die(json_encode($rs));
}

// prepare need form keys and patterns for checking...
$need_form_keys = [
    ['User name','username','^[a-zA-Z_]+([a-zA-Z\d_]+){1,32}$', 'Имя пользователя'],
    ['User password','userpassword','^([a-zA-Z\d@!_-]+){4,33}$', 'Пароль'],
    ['User password re','userpassword_re','^([a-zA-Z\d@!_-]+){4,33}$', 'Повтор пароля'],
    ['User mail','mail','^[a-zA-Z_]+[a-zA-Z_\d]*@[a-zA-Z\d_]+\.[a-zA-Z\d_]+', 'Емейл'],
    ['Captcha','sup_captcha','^[a-zA-Z\d]+$'],
];
$additional_form_keys = [
// empty

];
/////

$subject = "Message from main_site";
$msg_header = 'WarMaster - регистрация';

check_params($need_form_keys, $additional_form_keys);



//
//
// check password_re
if ($_POST['userpassword'] !== $_POST['userpassword_re']){
    $rs = [
        'success' => 0,
        'message' => 'Пароль и повтор пароля не совпадают!',
        'last_error' => 'userpassword_re',
    ];
    die(json_encode($rs));
}

$is_email_duplicate = is_email_duplicate($mysql, $_POST['mail']);
////
if ($is_email_duplicate['success'] === 0){
    die(json_encode($is_email_duplicate));
}

//
// $user_data = ['ivan','iPaa@@Sss1', 'ivi@gmail.com'];
$user_data['username']     = $_POST['username'];
$user_data['userpassword'] = $_POST['userpassword'];
$user_data['mail']         = $_POST['mail'];
$mail = $user_data['mail'];
$user_data = [];
$i_user_group = 2; // pust budet 2!
// generate hash for restore password here
$some_password_string = $_POST['username'] . $_POST['userpassword'] . $_POST['mail'] . rand(0,1000);
$reg_hash = password_hash ( $some_password_string, PASSWORD_BCRYPT);
//echo $reg_hash;
$user_data = [ $_POST['username'], $_POST['userpassword'], $_POST['mail'], $i_user_group, $reg_hash ];

$dbh = $mysql['connect'];

//
$captchaIsRight = $_SESSION['captcha2'] === $_POST['sup_captcha'];
if (!$captchaIsRight) {
    $res = ['success' => 0, 'message' => 'Неверная капча!',
        //'captcha' => $_SESSION['captcha2'],
        //'captcha_res' => $captchaIsRight
    ];
    die(json_encode($res));
}

/// заглушка
/// поставим тут заглушку, чтобы отладить анимацию на стороне клиента, чтобы там все было красиво
/// и его сразу переносило на форму с авторизацией и чтобы он видел что регистрация прошла успешно!
/// подготовлю тут ссылку с хешем для подтверждения пароля пользователя
///
$script_name = $_SERVER['PHP_SELF'];
$script_name = mb_substr($script_name, 0, mb_strrpos($script_name, '/')) . '/';
$restore_link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $script_name . 'confirmation.php?hash='.$reg_hash;
$restore_link_normal = "<a href=\"{$restore_link}\" target=\"_blank\">Подтвердить регистрацию!</a>";
$result['hash'] = $restore_link; $result['a'] = $restore_link_normal;
//$result['success'] = 2; $result['message'] = 'TEST_RESTORE_HASH!';
//die(json_encode($result));

$result = add_new_warmaster_user($dbh, $user_data);
if ($result['success'] === 0) return $result;

/// подготовка массива для отправки сообщения...
///
///
$mailData = [
    'subject' => 'WarMaster v102 - регистрация',
    'header_title' => [
        'Поздравляем, вы успешно зарегистрировались на сайте WarMaster',
        'Ссылка для подтверждения регистрации - ' . $restore_link_normal,
    ],
    'where_mail' => $_POST['mail'], // this mail need be a real!
    'whom_title' => 'новоиспеченному игроку',
];
mailSendMessage($mailData, $need_form_keys, $additional_form_keys);

$user_id = get_user_by_mail($dbh, $mail)['res'][0]['id'];
//echo Debug::d($user_id);

$set_st_chars = user_set_startup_chars($dbh, $user_id);
//echo Debug::d($set_st_chars);
if ($set_st_chars['success'] === 0) return $set_st_chars;

$result['a'] = $restore_link_normal;
$result['success'] = 1; $result['message'] = 'Зарегистрировались! Подтвердите регистрацию, перейдя по ссылке в присланном сообщении';
die(json_encode($result));

?>