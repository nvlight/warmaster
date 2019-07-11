$(document).ready(function () {
   console.log('document ready...');

   $('#warmaster_user_reg').submit(function (e) {
      e.preventDefault();
      console.log('warmaster_user_reg...');
      var warmaster_user_reg = $(this);
      var user_form_data = $(this).serialize();
      var reqUrl = './ajax/user_register.php';
      $.ajax({
          url: reqUrl,
          method: 'POST',
          data: user_form_data,
          dataType: 'json', // ! important string!
          beforeSend: function( xhr ) {
              console.log('before_send')
              warmaster_user_reg.css('opacity', '0.3');
              $('.animation_form_1').toggleClass('dn');
          },
          complete: function( xhr ) {
              //warmaster_user_reg.css('opacity', '1');
              //$('.animation_form_1').toggleClass('dn');
              console.log('after_send')
          },
      }).done(function (dt) {
          console.log('request is done');
          if (dt['success'] == 1){
              warmaster_user_reg.find('.msgs_show > *').html('');
              warmaster_user_reg.find('.success_message').html(dt['message']);
              setTimeout(function(){
                  warmaster_user_reg.css('opacity', '1');
                  $('.animation_form_1').toggleClass('dn');
              }, 700);
              // надо закрыть эту форму и открыть форму с авторизацией.
              $('.greeting .user-auth').click();
              $('#warmaster_user_auth .msgs_show .success_message').html(dt['message']);
          }else{
              warmaster_user_reg.css('opacity', '1');
              $('.animation_form_1').toggleClass('dn');
              warmaster_user_reg.find('.msgs_show > *').html('');
              warmaster_user_reg.find('.last_error').html(dt['message']);
          }

      }).fail(function () {
          warmaster_user_reg.css('opacity', '1');
          $('.animation_form_1').toggleClass('dn');
          console.log('error');
      });

      return false;
   });

   $('#warmaster_user_auth').submit(function (e) {
        e.preventDefault();
        console.log('warmaster_user_auth...');
        var warmaster_user_auth = $(this);
        var user_form_data = $(this).serialize();
        var authUrl = './ajax/user_auth.php';
        $.ajax({
            url: authUrl,
            method: 'POST',
            data: user_form_data,
            dataType: 'json', // ! important string!
            beforeSend: function( xhr ) {
                console.log('before_send')
                warmaster_user_auth.css('opacity', '0.3');
                $('.animation_form_2').toggleClass('dn');
            },
            complete: function( xhr ) {
                // warmaster_user_auth.css('opacity', '1');
                // $('.animation_form_2').toggleClass('dn');
                console.log('after_send')
            },
        }).done(function (dt) {
            console.log('request is done');
            //console.log(dt);
            //console.log($('#warmaster_user_reg'));
            //console.log(warmaster_user_reg);
            //console.log(dt['message']);
            //console.log(dt.message);
            if (dt['success'] == 1){
                warmaster_user_auth.find('.msgs_show > *').html('');
                warmaster_user_auth.find('.success_message').html(dt['message']);

                $('#warmaster_user_auth').css('opacity', '0.3');

                setTimeout(function(){
                    window.location.reload();
                }, 700);
            }else{
                warmaster_user_auth.css('opacity', '1');
                $('.animation_form_2').toggleClass('dn');
                warmaster_user_auth.find('.msgs_show > *').html('');
                warmaster_user_auth.find('.last_error').html(dt['message']);
            }

        }).fail(function () {
            console.log('error');
        });

        return false;
    });

   $('.greeting .user-reg, .greeting .user-auth').on('click', function () {
        $('.greeting > p ').toggleClass('dn');
        $('.form-block').toggleClass('dn');
   });

    $('.captcha_main').on('click', function(){
        console.log('we in: ' + $(this).attr('class'));

        $(this).attr('src', 'lib/captcha_inner.php');

    });
    $('.captcha_main2').on('click', function(){
        console.log('we in: ' + $(this).attr('class'));

        $(this).attr('src', 'lib/captcha_inner2.php');

    });

});