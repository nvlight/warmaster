<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.05.2019
 * Time: 19:33
 */

//
$WM_shops = [

];

# 1 изначально у нас магазин сантино в виде статического массива, внизу берем данные из БД
$santino_shop = [

    'input_classes' => ' name ',
    'input_name' => 'shopItem',
    'input_type' => 'radio',
//    'items' => [
//        [ 'name' => 'Дубинка', 'cost' => 130, 'type' => 1, 'value' => 5, 'type_caption' => 'Урон'],
//        [ 'name' => 'Полуторный меч', 'cost' => 250, 'type' => 1, 'value' => 10, 'type_caption' => 'Урон'],
//        [ 'name' => 'Двуручный меч', 'cost' => 500, 'type' => 1, 'value' => 15, 'type_caption' => 'Урон'],
//        [ 'name' => 'Охотничий нож', 'cost' => 120, 'type' => 1, 'value' => 'Охота', 'type_caption' => 'Урон'],
//
//        [ 'name' => 'Пластинчатый доспех', 'cost' => 600, 'type' => 2, 'value' => 10, 'type_caption' => 'Броня'],
//        [ 'name' => 'Кожаная броня', 'cost' => 200, 'type' => 2, 'value' => 5, 'type_caption' => 'Броня' ],
//
//        [ 'name' => 'Сырая сталь', 'cost' => 110, 'type' => 3, 'value' => 'Сырье', 'type_caption' => 'Сырье'],
//    ],

    'html' => '',
];

# 2
// подготовим элементы для магазина
// try to get shops_with_childs
$dbh = $mysql['connect'];
$shop_with_childs_rs = user_get_shops_with_childs($dbh);
//echo Debug::d($shop_with_childs_rs,'',1); //die;

$new_santito_items = [];
if ($shop_with_childs_rs['success'] === 1)
foreach($shop_with_childs_rs['result'] as $k => $v)
if ( intval($v['i_shop']) === 1)
{
    $item = [];

    $item['name'] = $v['item_name'];
    $item['cost'] = $v['cost'];
    $item['item_id'] = $v['i_item'];
    $item['type_caption'] = $v['item_type_name'];
    $item['type'] = $v['i_item_type'];
    $item['value'] = $v['item_value'];

    $new_santito_items[] = $item;
}
$santino_shop['items'] = $new_santito_items;
//echo Debug::d($santino_shop,''); die;


$santino_shop_html = '';
foreach($santino_shop['items'] as $k => $v){
    $tmp = <<<INPUT
<input class="{$santino_shop['input_classes']}" data-itemid="{$v['item_id']}" type="{$santino_shop['input_type']}" value="{$v['name']}" name="{$santino_shop['input_name']}"> {$v['name']} - <span>{$v['cost']}</span> ({$v['type_caption']} <em>{$v['value']}</em>)
INPUT;
    $santino_shop_html .= '<li><label>' . $tmp . "." . "</label></li>\n";
}
$santino_shop['html'] = "<ul>" . $santino_shop_html ." </ul>" ;
//echo Debug::d($santino_shop['html'],'santino shop - html'); die;

$WM_shops[] = $santino_shop;
//echo Debug::d($WM_shops[0]['html']); die;

//
$WM_user_inventory = user_inventory_get($dbh);
//echo Debug::d($WM_user_inventory); die;

// test
// test this equipment function...
//$ued = user_equipment_do($dbh, $_SESSION['user']['id'], 3);
//echo Debug::d($ued,'ued',1);
//die;

// test
// user_set_hero_chars($dbh, $user_id, $attack=0, $armor=0) --> test
//$attack = 10; $attack = 77;
//$armor  = 0; $armor = 25;
//$ushc = user_set_hero_chars($dbh, $_SESSION['user']['id'], $attack, $armor);
//echo Debug::d($ushc,'$ushc'); die;
