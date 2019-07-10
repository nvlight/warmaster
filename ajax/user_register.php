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
$user_data = [ $_POST['username'], $_POST['userpassword'], $_POST['mail'], $i_user_group ];

$dbh = $mysql['connect'];

$captchaIsRight = $_SESSION['captcha2'] === $_POST['sup_captcha'];
if (!$captchaIsRight) {
    $res = ['success' => 0, 'message' => 'Неверная капча!',
        //'captcha' => $_SESSION['captcha2'],
        //'captcha_res' => $captchaIsRight
    ];
    die(json_encode($res));
}

$result = add_new_warmaster_user($dbh, $user_data, $need_form_keys, $additional_form_keys, $subject, $msg_header);

$user_id = get_user_by_mail($dbh, $mail)['res'][0]['id'];
//echo Debug::d($user_id);

$set_st_chars = user_set_startup_chars($dbh, $user_id);
//echo Debug::d($set_st_chars);
if ($set_st_chars['success'] === 0) return $set_st_chars;

$result['success'] = 1; $result['message'] = 'Зарегистрировались!';
die(json_encode($result));

?>