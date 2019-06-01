<?php

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/user_db_functions.php';

if (!array_key_exists('app_start', $_SESSION)){
    $rs = ['success' => 0, 'message' => 'something gone wrong!'];
    die(json_encode($rs));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $rs = ['success' => 0, 'message' => 'something gone wrong again!'];
    die(json_encode($rs));
}


// prepare need form keys and patterns for checking...
$need_form_keys = [
    ['Stage','stage','^-?\d{1,3}$'],
    ['message','hash',''],
    //['Captcha','sup_captcha','^[a-z\d]+$'],

];
$additional_form_keys = [
    // empty

];

/////

$subject = "Message from main_site";
$msg_header = 'Запрос - получить консультацию!';

check_params($need_form_keys, $additional_form_keys);

//
//
//
switch (intval($_SESSION['user']['stage'])){
    case -1:
            $_SESSION['user']['stage'] = 0;
            break;
    case 0:
            $_SESSION['user']['stage'] = 1;
            // мы на пути в город хоринис, за проход с нас берут 200 золота
            $_SESSION['user']['gold']-=200;

            // save 2 zhournal - go to horinis - guards - -200$
$curr_stage = <<<ZH
'<ul class="Horinis">
 	<li><span class="QuestTitle">Хоринис</span><br> - Чертов охранник содрал с меня 200 золотых, чтобы я мог попасть в город, нужно искать работу</li>
</ul>'
ZH;
            zhournal_set($mysql['connect'], $_SESSION['user']['id'], $curr_stage);
            // save resourses
            $new_res = $_SESSION['user'];
            $new_res = json_encode($new_res);
            update_user_resourses($mysql['connect'], intval($_SESSION['user']['id']), $new_res);
            break;

    default:
}
//$_SESSION['user']['stage']++;

//
$rs = ['success' => 1, 'message' => 'we recive the current stage!',
    'stage' => $_SESSION['user']['stage'],
];
die(json_encode($rs));

?>