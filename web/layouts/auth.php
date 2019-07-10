<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <title>Warmaster</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
<!--    <link href="https://fonts.googleapis.com/css?family=Kurale&amp;subset=cyrillic,cyrillic-ext" rel="stylesheet">-->

    <script src="js/jquery-3.3.1.min.js"></script>

    <script src="js/auth.js"></script>

</head>

<body>


<div class="dn">

    <div id="melody-1" class="player" data-src="audio/gothic.mp3"></div>
    <audio id="my-hidden-player" loop></audio>
    <div style="background-color: #fff;">
        <?php
        echo Debug::d($_SESSION,'SESSION');
        ?>
    </div>
    <div>
        <button id="set_stage_0">
            go to start!
        </button>

    </div>

</div>



<div class="reg-cont">
    <div class="game-logo">
        <img src="img/gamelogo2.png" alt="">
    </div>
    <div class="form-block user-reg dn">
        <h1>Регистрация</h1>
        <form method="POST" id="warmaster_user_reg">
            <label>Логин:</label>
            <input type="text" name="username"/ autofocus required pattern="^[a-zA-Z_]+([a-zA-Z\d_]+){1,32}$" title='Ivan' value=''>

            <label>Пароль:</label>
            <input type="password" name="userpassword" required pattern="^([a-zA-Z\d_@!#$+\d-]+){4,33}$" title="some_password" value="" />

            <label>Повторите пароль:</label>
            <input type="password" name="userpassword_re" required pattern="^([a-zA-Z\d_@!#$+\d-]+){4,33}$" title="some_password_re_enter" value=""/>

            <label>mail:</label>
            <input type="text" name="mail" required pattern="^[a-zA-Z_]+[a-zA-Z_\d]*@[a-zA-Z\d_]+\.[a-zA-Z\d_]+" title="some_mail@gmail.yes" value=""/>

            <div class="msgs_show">
                <p class="success_message"></p>
                <p class="last_error"></p>
            </div>

            <div class="form-block_inner">
                <button class="form-block_btn" type="submit" name="loginSubmit">Зарегистрироваться!</button>
            </div>
        </form>
        <div class="animation_form_1 dn">
            <span class="one"></span>
            <span class="two"></span>
            <span class="three"></span>
            <span class="four"></span>
        </div>
    </div>
    
    <div class="form-block user-auth ">
        <h1>Авторизация</h1>
        <form method="POST" id="warmaster_user_auth">
            <label>Емейл:</label>
            <input type="text" name="mail" autofocus required pattern="^[a-zA-Z_]+[a-zA-Z_\d]*@[a-zA-Z\d_]+\.[a-zA-Z\d_]+" title="some_mail@gmail.yes" value=""/>

            <label>Пароль:</label>
            <input type="password" name="userpassword" required pattern="^([a-zA-Z\d_@!#$+\d-]+){4,33}$" title="some_password" value="" />

            <div class="msgs_show">
                <p class="success_message"></p>
                <p class="last_error"></p>
            </div>

            <div class="form-block_inner">
                <button class="form-block_btn" type="submit" name="loginSubmit">Войти</button>
            </div>
        </form>
        <div class="animation_form_2 dn">
            <span class="one"></span>
            <span class="two"></span>
            <span class="three"></span>
            <span class="four"></span>
        </div>
    </div>

    <div class="greeting">
        <p class="dn">Готов продолжить приключение? <a class="btn_toggle user-auth">Авторизоваться!</a> </p>
        <p class="">Новый игрок? <a class="btn_toggle user-reg ">Зарегистрироваться!</a> </p>
    </div>
</div>


</body>

</html>
