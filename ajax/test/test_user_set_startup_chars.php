<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';

$dbh = $mysql['connect'];

$user_data['username']     = 'ivan_testovich';
$user_data['userpassword'] = '1111';
$user_data['mail']         = 'iduso@mail.ru';
$mail = $user_data['mail'];

$i_user_group = 2; // pust budet 2!
$user_data = [ $user_data['username'], $user_data['userpassword'], $user_data['mail'], $i_user_group ];

$need_form_keys = [];
$additional_form_keys = [];

$subject = "Message from main_site";
$msg_header = 'WarMaster - регистрация';

echo Debug::d($user_data);
$result = add_new_warmaster_user($dbh, $user_data, $need_form_keys, $additional_form_keys, $subject, $msg_header);
echo Debug::d($result);

$user_id = get_user_by_mail($dbh, $mail)['res'][0]['id'];
echo Debug::d($user_id);

$set_st_chars = user_set_startup_chars($dbh, $user_id);
echo Debug::d($set_st_chars);
