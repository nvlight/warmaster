<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 12.05.2019
 * Time: 16:43
 */

session_start();

require '../db/mysql.config.php';
require '../db/mysql.connect.php';
require '../vendor/autoload.php';
require '../lib/functions.php';
require '../ajax/db_functions_part_1.php';
require '../ajax/db_functions_part_2.php';


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
    //['stage','stage','^\d{1,5}$','Введите текущий прогресс!'],
    //['Captcha','sup_captcha','^[a-z\d]+$'],

];
$additional_form_keys = [
    // empty

];

//echo Debug::d($_POST);

/////
check_params($need_form_keys, $additional_form_keys);

//
//echo Debug::d($_REQUEST);
$dbh = $mysql['connect'];
$user_id = $_SESSION['user']['id'];

//var HeroWeaponEquiped = $('#hero_weapon span').html();
//if (trainResolution == false) {
//    $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Кто ты такой? Я не тренирую всех подряд!' + '</p>');
//    $('.master .db').fadeIn();
//} else if (HeroPowerInner >= 5) {
//    $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Ты достаточно силен, мне больше нечему тебя учить' + '</p>');
//    MasterDb();
//} else if (trainResolution == true && HeroWeaponEquiped == 'Пусто') {
//    $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Онар хорошо отзывался о тебе. У тебя есть оружие? возвращайся когда будет с чем тренироваться!' + '</p>');
//    MasterDb();
//} else if (trainResolution == true && HeroWeaponEquiped !== 'Пусто') {
//    if (HeroWeaponEquiped == 'Дубинка') {
//        $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Дубинкой можешь крыс в лесу погонять! Возвращайся с достойным оружием!' + '</p>');
//        MasterDb();
//    } else if (HeroGoldInner < 200) {
//        $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Тренировка стоит 200 монет, возваращайся когда будет чем платить!' + '</p>');
//        MasterDb();
//    } else if (HeroWeaponEquiped !== 'Ржавый меч' && HeroWeaponEquiped !== 'Дубинка' && HeroGoldInner >= 200) {
//        TimerFunc(10, HeroGold, HeroGoldInner = HeroGoldInner - 200, 'Тренировка: ', 'Твоя сила увеличилась на 1');
//        HeroPowerInner = HeroPowerInner + 1;
//        HeroPower.innerHTML = HeroPowerInner;
//        dialogBg('url(./img/traning.jpg)');
//    }
//}

$rs = lares_training($dbh,$user_id);

//echo Debug::d($rs);
die(json_encode($rs));