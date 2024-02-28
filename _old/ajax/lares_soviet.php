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

//if (trainResolution == true) {
//    if (sitizen != true) {
//        if (HaraldMission == true) {
//            $('.master .db .dinamicTxt').html('<p class="LarsTxt LarsTxtFirst" id="QuestionToLars-1">' + 'На что влияет сила?' + '</p>' + '<p class="LarsTxt LarsTxtSecond" id="QuestionToLars-3">' + 'Какую броню лучше носить?' + '</p>' + '<p class="LarsTxt" id="QuestionToLars-2">' + 'Как стать гражданином Хориниса?' + '</p>');
//        }
//    } else {
//        if (HornOfMrakoris == true) {
//            $('.master .db .dinamicTxt').html('<p class="LarsTxt LarsTxtFirst" id="QuestionToLars-1">' + 'На что влияет сила?' + '</p>' + '<p class="LarsTxt LarsTxtSecond" id="QuestionToLars-3">' + 'Какую броню лучше носить?' + '</p>' + '<p class="LarsTxt" id="QuestionToLars-4">' + 'Что можешь рассказать о Мракорисе?' + '</p>');
//        }
//        if (HornOfMrakoris != true && HaraldMission != true) {
//            $('.master .db .dinamicTxt').html('<p class="LarsTxt LarsTxtFirst" id="QuestionToLars-1">' + 'На что влияет сила?' + '</p>' + '<p class="LarsTxt LarsTxtSecond" id="QuestionToLars-3">' + 'Какую броню лучше носить?' + '</p>');
//        }
//    }
//    $('.master .db').fadeIn();
//    $('#QuestionToLars-1').click(function () {
//        $('.master .db .dinamicTxt').html('<p>' + 'Сила увеличивает мощь твоих ударов!' + '</p>');
//    });
//    $('#QuestionToLars-3').click(function () {
//        $('.master .db .dinamicTxt').html('<p>' + 'Тяжелая броня делает тебя крепче, но в ней ты более медлительный и быстрее устаешь, в некоторых ситуациях в тяжелом снаряжении ты будешь более уязвимым.' + '</p>');
//    });
//    $('#QuestionToLars-4').click(function () {
//        $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Опасный зверь, но довольно медлительный. Даже не думай подобраться незаметно, учуит за сотню шагов. Если уж встретился  с этой зверюгой лицом к лицу, обращай внимание на первый удар, если схватил большой урон, немедленно отступай!' + '</p>');
//        var HaraldQuestMrakoris = '<span class="QuestTitle">' + 'Рог Мракориса' + '</span>';
//        var HaraldQuestMrakorisTxt = '<li>' + ' - Ларес сказал, чтобы победить Мракориса надо избегать его критической атаки или вовремя отступить' + '</li>';
//        QuestListArr(HaraldQuestMrakoris, HaraldQuestMrakorisTxt, '.HaraldQuestWeapon');
//    });
//    $('#QuestionToLars-2').click(function () {
//        sitizen = true;
//        $('.master .db .dinamicTxt').html('<p class="citizen">' + 'Чтобы стать гражданином, кто то из влиятельных жителей города должен за тебя поручиться!' + '</p>' + '<button class="btn LaresQuest">' + 'Помоги стать гражданином...' + '</button>');
//        $('.LaresQuest').click(function () {
//            $('.master .db .dinamicTxt').html('<p>' + 'Ты должен проявить себя в каком либо деле, скажем охотничем... Добудь мне три хвоста болотной крысы и две волчьи шкуры.' + '</p>');
//            var LaresQuest = '<span>' + 'Задание Лареса' + '</span>';
//            var LaresQuestTxt = '<li>' + '- Ларес поможет мне стать гражданином, но для этого я должен добыть для него 2 хвоста болотной крысы и 3 волчьи шкуры' + '</li>';
//            QuestListArr(LaresQuest, LaresQuestTxt, '.HaraldQuest');
//            $('.lares_btn').append('<button class="btn" id="PassLarsQuest">Сдать задание</button>');
//
//            $('#PassLarsQuest').click(function () {
//                var ItemIndexRatTail = HeroItem[0].indexOf('Хвост крысы');
//                var ItemIndexWoolfSkin = HeroItem[0].indexOf('Волчья шкура');
//                if (ItemIndexRatTail != -1 && ItemIndexWoolfSkin != -1) {
//                    var CountTail = $('.counter-' + (ItemIndexRatTail)).html();
//                    var CountSkin = $('.counter-' + (ItemIndexWoolfSkin)).html();
//
//                    if (CountTail >= 2 && CountSkin >= 3) {
//                        $('.master .db .dinamicTxt').html('Ларес: Ну чтож, мы поздравления, ты теперь гражданин Хориниса!');
//                        $('.db_lares').fadeIn();
//                        $("#PassLarsQuest").remove();
//                        PassTheItems(ItemIndexRatTail, 2);
//                        PassTheItems(ItemIndexWoolfSkin, 3);
//                        AccessToTheForge = true;
//                        var LaresQuestPass = '<span>' + 'Я гражданин' + '</span>';
//                        var LaresQuestPassTxt = '<li>' + '- Ларес поручился за меня, я теперь гражданин Хориниса!' + '</li>';
//                        QuestListArr(LaresQuestPass, LaresQuestPassTxt, '.HaraldQuest');
//                    } else {
//                        $('.master .db .dinamicTxt').html('Условие не выполнено!');
//                        $('.db_lares').fadeIn();
//                    }
//                } else {
//                    $('.master .db .dinamicTxt').html('Условие не выполнено!');
//                    $('.db_lares').fadeIn();
//                }
//            });
//        });
//    });
//} else {
//    $('.master .db .dinamicTxt').html('<p>' + 'Ларес: Думаешь я раздаю советы каждому встречному!' + '</p>');
//    $('.master .db').fadeIn();
//}

$rs = lares_soviet($dbh,$user_id);

//echo Debug::d($rs);
die(json_encode($rs));