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
//echo Debug::d($shop_with_childs_rs,'',1);
//die;

$new_santito_items = [];
foreach($shop_with_childs_rs['result'] as $k => $v)
if ( intval($v['i_shop']) === 1)
{
    $item = [];

    $item['name'] = $v['name'];
    $item['cost'] = $v['cost'];
    $item['item_id'] = $v['i_item'];
    if ($v['spec_type'] !== ''){
        $item['type'] = 3;
        $item['value'] = $v['spec_type'];
        $item['type_caption'] = $item['value'];
    }else{
        $armor  = $v['armor'] + 0;
        $attack = $v['attack'] + 0;
        //echo Debug::d($armor,'',2);
        //echo Debug::d($attack,'',2);
        // die;
        if ($attack > 0){
            $item['type'] = 1;
            $item['value'] = $attack;
            $item['type_caption'] = 'Урон';
        }else{
            $item['type'] = 2;
            $item['value'] = $armor;
            $item['type_caption'] = 'Броня';
        }
    }

    $new_santito_items[] = $item;
}
$santino_shop['items'] = $new_santito_items;
//die;


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



/// # TMP -
/// уже занесено в основной файл с функциями
/// $i_item = 7; $user_id = $_SESSION['user']['id'];
//tmp_inventory_buy_item($dbh, $user_id, $i_item);
function tmp_inventory_buy_item($dbh, $user_id, $i_item)
{
    /// ####
    ///
    //echo Debug::d($_SESSION);
    $curr_shop_item['id'] = $i_item;
    $curr_shop_item['inner'] = user_get_shopitem_by_id($dbh, $curr_shop_item['id']);
    //echo Debug::d($curr_shop_item); die;
    $curr_shop_item['price'] = $curr_shop_item['inner']['result'][0]['cost'] * 1;
    //echo Debug::d($curr_shop_item['price']);

    // # 1 new ---> test user_add_item
    // добавление и обновление итемов по ИД работает!
    $ruait = user_inventory_add_item($dbh, $curr_shop_item['id']);
    //echo Debug::d($ruait,'');
    if ($ruait['success'] === 1){
        $user['curr_gold'] = user_get_gold($dbh, $user_id);
        //echo Debug::d($user['curr_gold'],'',1);
        if ($user['curr_gold']['success'] === 1){
            $cu_gold = $user['curr_gold']['res'][0]['gold'] * 1;
            $nu_gold = $cu_gold - $curr_shop_item['price'];
            //echo Debug::d($cu_gold,'$cu_gold',2);
            //echo Debug::d($nu_gold,'$nu_gold',2);
            $usg = user_set_gold($dbh, $user_id, $nu_gold);
            //echo Debug::d($usg,'',2);
        }
    }
    //die;

    // # 2 new ---> test user_del_item_by_ID
    // удаление и обновление итемов по ИД тоже работает!
    //$ruaig = user_inventory_del_item($dbh, 5);
    //die;
}
