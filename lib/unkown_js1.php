<?php

//function get_current_stage(){
//    var stage = -1;
//    console.log('find current stage: ');
//    var url = 'ajax/get_curr_stage.php';
//
//    $.ajax({
//            'type': 'post',
//            'url': url,
//            'data': '',
//            'dataType': 'json',
//            'async': false,
//            'beforeSend': function(xhr) {
//        //
//    }
//        }).done(function(rs) {
//        console.log("ответ получен: " + rs['message'] + ' || success: ' + rs['success'] + ' || stage : ' + rs['stage'])
//            if (rs['success'] === 1){
//                stage = rs['stage'];
//            }
//        }).fail(function() {
//        console.log("Ошибка!")
//        });
//
//        console.log('stage after: ' + stage);
//        return stage;
//    }