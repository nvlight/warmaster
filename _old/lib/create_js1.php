<?php

//echo Debug::d($_SESSION['user']);



$user_res_keys = [
    'gold', 'health', 'armour_count', 'critical', 'power', 'damage', 'weapon', 'armour_item'
];

$user_res_vals = [];

foreach($user_res_keys as $k => $v){

    if (array_key_exists($v, $_SESSION['user'])){
        $user_res_vals[$v] = $_SESSION['user'][$v];
        //echo $k . ' : ' . $v; echo "<br>";
    }
}
$user_res_vals = json_encode($user_res_vals);


$js1 = <<<JS1

<script>
function get_user_res_by_key(key) {
  
    var user_res = $user_res_vals;
    //console.log(user_res);
    return user_res[key];
}
</script>

JS1;

//return {$_SESSION['user']['stage']};
$js2 = <<<JS2

<script>

    function get_current_stage() {
        //console.log('im here!');
        //alert('weaw!');        
        return 0;
    }
        
</script>
JS2;

return $js1 . $js2;
