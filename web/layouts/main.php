<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<title>Warmaster</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
	<link rel="stylesheet" href="css/style.css">
<!--	<link href="https://fonts.googleapis.com/css?family=Kurale&amp;subset=cyrillic,cyrillic-ext" rel="stylesheet">-->
    <script src="js/jquery-3.3.1.min.js"></script>


</head>

<body>

	<div id="melody-1" class="player" data-src="audio/gothic.mp3"></div>
	<audio id="my-hidden-player" loop></audio>
    <div class="debug__right-menu">
        <p class="mb10"><a href="#" id="hero_data-right-debug-block">Обновить!</a></p>
        <div id="hero_data">
            <?php if ($user_data['success'] !== 0): ?>
                <?php foreach($user_data['res'] as $hk => $hv ): ?>
                    <strong><?=$hk?></strong>: <?=$hv?> <br>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php ?>
        </div>
    </div>
    <div style="background-color: #fff;">
        <?php
        //echo Debug::d($_SESSION);
        //echo Debug::d($user_get_equipment,'',1);

        // найдем тут экиппировку и html для ее вывода.
        // также тут пропишем названия оружия и/или доспехов, если таковые есть
        // ### ! сначала нужно выводить броню ! Это от дизайна артефакт
        $weapon_column = "Пусто"; $equip_weapon = null;
        $armor_column  = "Пусто"; $equip_armor = null;

        if ($user_get_equipment['success'] == 1)
        foreach ($user_get_equipment['result'] as $k => $v)
        {
            //
            if (intval($v['i_item_type']) === 1){
                $equip_weapon['name'] = $v['name']; $equip_weapon['i_item'] = intval($v['i_item']);
                $weapon_column = $v['name'];
            } elseif (intval($v['i_item_type']) === 2){
                $equip_armor['name'] = $v['name'];  $equip_armor['i_item']  = intval($v['i_item']);
                $armor_column = $v['name'];
            }
        }

        ?>
    </div>
    <div class="user-top-menu">
        <div class="logo">
            <button id="set_stage_0">
                Начать игру заново
            </button>
            <button id="getHeroPower">
                getHeroChars
            </button>
        </div>

        <div class="user-items">
            <span>
                <span class="herohp1">Health: </span>
                <span id="hero_hp"></span>
                <span class="dollar1">Gold: </span>
                <span id="hero_gold">
                    <?php
                    $user_id = intval($_SESSION['user']['id']);
                    $dbh = $mysql['connect'];

                    $curr_gold = user_get_gold($dbh, $user_id);
                    //echo Debug::d($curr_gold,'',2);
                    //echo $curr_gold['res'][0]['gold'];
                    ?>
                </span>
            </span>
            <span class="username1">Username: </span>
            <span class="username-class">
                <?=$_SESSION['user']['username']?>
            </span>
            <button id="user_logout">
                Выйти
            </button>
        </div>

    </div>
	<div class="container main_div dn">
		<!-- <div class="timer_box border">
			<span id="my_timer" style="color: #f00; font-size: 150%; font-weight: bold;">10:00</span>

			<script>
				startTimer();
			</script>
		</div> -->
		<div class="box-top">
			<!-- Лавка -->
			<div class="box-item border marketPlace">
				<div class="npc_name">
					<p><b>Сантино (Торговец)</b></p>
				</div>
				<div class="shop_box bg_inner" id="shop_box">
                    <?=$WM_shops[0]['html']?>
				</div>
				<div class="master_btn__box">
					<button class="btn" id="bye" type="button">Купить</button>
					<button class="btn" id="ShowTheProduct" type="button">Товары</button>
					<button class="btn" id="talkToSeller" type="button">Говорить</button>
				</div>
				<div class="dialog_box db db_market bg-img">
					<div class="db_close">х</div>
					<div class="dinamicTxt"></div>
				</div>
			</div>

			<!-- Персонаж -->
			<div class="box-item hero border">
				<div class="stata">
					<div class="npc_name"><b>Персонаж</b></div>
					<div class="bg_inner">
						<p class="hero_st">Характеристики:</p>
						<div class="stata_inner">
							<div class="stata_left">
								<p>Сила: <span id="hero_power"></span></p>
								<p>Урон: <span id="hero_atack"></span></p>
							</div>
							<div class="stata_right">
								<p>Броня: <span id="hero_armor"></span></p>
								<p>Крит: <span id="hero_krit"></span></p>
							</div>
						</div>

                        <p class="hero_st hero_item__eqiped" id="hero_weapon">Оружие: <br> <span><?=$weapon_column?></span></p>
                        <p class="hero_st hero_item__eqiped" id="hero_armor_equiped">Доспех: <br> <span><?=$armor_column?></span></p>

					</div>
				</div>
                <?php


                /// $equip_weapon
                ///
                $st_db = "style='display: block;'";
                if ($user_get_equipment['success'] === 1) {
                    // for armor
                    if ($equip_armor)
                    {
                        switch ($equip_armor['i_item']) {
                            case 5:
                                ?>
                                <div class="leather-armor Hero_Armor" <?= $st_db ?> ><img src="img/leather-armor.png" alt=""></div>
                                <div class="heavy-armor Hero_Armor"><img src="img/heavy-armor.png" alt=""></div>
                                <div class="armor-crow Hero_Armor"><img src="img/crow-armor.png" alt=""></div>
                                <?php
                                break;
                            case 6:
                                ?>
                                <div class="leather-armor Hero_Armor"><img src="img/leather-armor.png" alt=""></div>
                                <div class="heavy-armor Hero_Armor" <?= $st_db ?> ><img src="img/heavy-armor.png" alt=""></div>
                                <div class="armor-crow Hero_Armor"><img src="img/crow-armor.png" alt=""></div>
                                <?php
                                break;
                            case 9:
                                ?>
                                <div class="leather-armor Hero_Armor"><img src="img/leather-armor.png" alt=""></div>
                                <div class="heavy-armor Hero_Armor"><img src="img/heavy-armor.png" alt=""></div>
                                <div class="armor-crow Hero_Armor" <?= $st_db ?> ><img src="img/crow-armor.png" alt=""></div>
                                <?php
                                break;
                            default:
                                ?>
                                <div class="leather-armor Hero_Armor"><img src="img/leather-armor.png" alt=""></div>
                                <div class="heavy-armor Hero_Armor"><img src="img/heavy-armor.png" alt=""></div>
                                <div class="armor-crow Hero_Armor"><img src="img/crow-armor.png" alt=""></div>

                            <?php
                        }
                    }

                    // for weapon

                    $st_db = "style='display: block;'";

                    /// for weapon
                    ///
                    if ($equip_weapon) {
                        switch ($equip_weapon['i_item']) {
                            case 1:
                                ?>
                                <div class="stick Hero_Weapon" <?= $st_db ?> ><img src="img/dubinka.png" alt=""></div>
                                <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>
                                <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>
                                <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>
                                <?php
                                break;
                            case 2:
                                ?>
                                <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>
                                <div class="sword Hero_Weapon" <?= $st_db ?> ><img src="img/sword.png" alt=""></div>
                                <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>
                                <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>
                                <?php
                                break;
                            case 3:
                                ?>
                                <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>
                                <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>
                                <div class="long-sword Hero_Weapon" <?= $st_db ?> ><img src="img/longsword.png" alt=""></div>
                                <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>
                                <?php
                                break;
                            case 8:
                                ?>
                                <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>
                                <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>
                                <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>
                                <div class="ripper Hero_Weapon" <?= $st_db ?> ><img src="img/ripper.png" alt=""></div>
                                <?php
                                break;
                            default:
                                ?>
                                <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>
                                <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>
                                <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>
                                <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>
                            <?php
                        }
                    }
                    //
                }else{
                ?>
                    <div class="leather-armor Hero_Armor"><img src="img/leather-armor.png" alt=""></div>
                    <div class="heavy-armor Hero_Armor"><img src="img/heavy-armor.png" alt=""></div>
                    <div class="armor-crow Hero_Armor"><img src="img/crow-armor.png" alt=""></div>

                    <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>
                    <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>
                    <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>
                    <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>
                <?php
                }
                ?>

<!--                <div class="leather-armor Hero_Armor"><img src="img/leather-armor.png" alt=""></div>-->
<!--                <div class="heavy-armor Hero_Armor"><img src="img/heavy-armor.png" alt=""></div>-->
<!--                <div class="armor-crow Hero_Armor"><img src="img/crow-armor.png" alt=""></div>-->
<!---->
<!--                <div class="stick Hero_Weapon"><img src="img/dubinka.png" alt=""></div>-->
<!--                <div class="sword Hero_Weapon"><img src="img/sword.png" alt=""></div>-->
<!--                <div class="long-sword Hero_Weapon"><img src="img/longsword.png" alt=""></div>-->
<!--                <div class="ripper Hero_Weapon"><img src="img/ripper.png" alt=""></div>-->

                <div class="master_btn__box">
					<button class="btn" type="button" class="btnJournal" id="journal">Журнал</button>
                    <button class="btn" type="button" class="btnDropWeapon" id="DropWeapon">Положить оружие</button>
                    <button class="btn" type="button" class="btnDropArmor" id="DropArmor">Снять броню</button>
				</div>
				<div class="hero_img"><img src="img/hero.png" alt=""></div>
			</div>

			<!-- Дом -->
			<div class="box-item home border">
				<div class="npc_name"><b>Дом</b></div>
				<div class="bg_inner">
					<p class="hero_st">Сундук:</p>
					<div class="inventory" id="inventory">
<!--						<div id="counter"></div>-->
<!--						<ul id="inventory"></ul>-->
                        <?php if ($WM_user_inventory['success'] !== 0): ?>
                            <?php //echo Debug::d($WM_user_inventory,'',2); ?>
                            <?=$WM_user_inventory['result']?>
                        <?php endif; ?>
					</div>
				</div>
				<div class="master_btn__box">
					<div class="HomeMessageAlert">Невозможно экипировать</div>
					<button class="btn" id="equipItem" type="button">Экипировать</button>
					<button class="btn" id="sellItem" type="button">Продать</button>
					<button class="btn" id="toRest" type="button">Отдыхать</button>
				</div>
                <div class="dialog_box db db_market bg-img" style="display: none;">
                    <div class="db_close">х</div>
                    <div class="dinamicTxt"><p>Торговец: Ты не выбрал предмет для продажи :)</p></div>
                </div>
			</div>

		</div>
		<!-- end box-top -->

		<div class="box_middle">
			<!-- Ларес -->
			<div class="box-item master border">
				<div class="npc_name"><b>Ларес (Мастер меча)</b></div>
				<div class="npc lares"><img src="img/lars.png" alt=""></div>
				<div class="master_btn__box lares_btn">
					<button class="btn" id="btn_master" type="submit">Тренироваться</button>
					<button class="btn" id="btn_advice" type="submit">Совет</button>
				</div>
				<div class="dialog_box db db_lares bg-img">
					<div class="db_close">х</div>
					<div class="dinamicTxt"></div>
				</div>
			</div>

			<!-- Противник -->
			<div class="box-item enemy border">
				<div class="npc_name"><b>Туманная лощина</b></div>
				<!-- <div class="bg_inner bg_inner__ork">
					<p>Сила:<span>???</span></p>
					<p>Броня:<span>???</span></p>
					<p>Критический удар:<span>???</span></p>
					<p>Здоровье:<span>???</span></p>
					<p>Экипировка:<span>???</span></p>
				</div> -->
				<div class="master_btn__box">
					<button class="btn" id="FoggyHollow">Разведать</button>
					<!-- <button class="btn" id="ork">Орк</button> -->
					<!-- <button class="btn" id="btn_enemy">Сходить на разведку</button> -->
				</div>
				<div class="dialog_box db-hollow bg-img">
					<div class="db_close">х</div>
					<div id="dinamicTxtHollow"></div>
				</div>
			</div>

			<!-- Ферма Онара -->
			<div class="box-item farm border" id="farm">
				<div class="npc_name"><b>Ферма Онара</b></div>
<!--				<div class="master_btn__box" id="div">-->
<!--					<div class="tooltip">-->
<!--						<span>Ты не знаешь где Онар</span>-->
<!--					</div>-->
<!--					<div class="tooltip2">-->
<!--						<span>Говори с охраной</span>-->
<!--					</div>-->
<!--					<button class="btn" id="btn_farmeGuard" type="button">Сентеза (Охрана)</button>-->
<!--					<button class="btn" id="btn_onar" type="button">Онар</button>-->
<!--					<button class="btn" id="btn_workFarm" type="button">Работать</button>-->
<!--				</div>-->
<!--				<div class="dialog_box db_1 min_db" id="static-db">-->
<!--					<div class="db_close">х</div>-->
<!--					<p>Сентеза: Что тебе нужно? Хочешь пройти дальше, плати 100 монет!</p>-->
<!--					<button class="btn" id="btn_pay_senteza">Согласен</button>-->
<!--					<button class="btn" id="btn_not_pay_senteza">Послать к черту!</button>-->
<!--					<button class="btn">Уйти</button>-->
<!--				</div>-->
<!--				<div class="dialog_box min_db" id="dinamicDbSenteza">-->
<!--					<div class="db_close">х</div>-->
<!--					<div id="dinamicTxtSenteza"></div>-->
<!--					<div class="btn" id="btnNextSenteza"></div>-->
<!--				</div>-->
                <?php
                $ugs = user_get_stage($dbh, $_SESSION['user']['id']);
                if ($ugs['success'] === 1) {
                    $stage = intval($ugs['res'][0]['stage']);
                }else{
                    $stage = 1;
                }
                ?>
                <div class="master_btn__box" id="div">
                    <button class="btn" id="btn_farmeGuard" type="button">Сентеза (Охрана)</button>
                    <button class="btn <?php if ($stage <= 4) echo 'dn'; ?>"  id="btn_onar" type="button">Онар</button>
                    <button class="btn <?php if ($stage === 1) echo 'dn'; ?>" id="btn_workFarm2" type="button">Работать</button>
                    <button class="btn dn" id="btn_workFarm" type="button">Работать</button>
                </div>
                <div class="dialog_box db_1 min_db" id="static-db">
                    <div class="db_close">х</div>
                    <p>Сентеза: Что тебе нужно? Хочешь пройти дальше, плати 100 монет!</p>
                    <button class="btn" id="senteza_pay" data-pay="1">Согласен</button>
                    <button class="btn" id="senteza_not_pay" data-pay="2">Послать к черту!</button>
                    <button class="btn" id="senteza_go_away">Уйти</button>
                </div>
                <div class="dialog_box min_db" id="dinamicDbSenteza">
                    <div class="db_close">х</div>
                    <div id="dinamicTxtSenteza"></div>
                    <div class="btn" id="btnNextSenteza"></div>
                </div>
			</div>
		</div>

		<div class="box_bottom">

			<!-- Лес -->
			<div class="box-item border wood">
				<div class="npc_name"><b>Лес</b></div>
				<div class="master_btn__box">
					<button class="btn" type="button" id="rat">Болотные крысы</button>
					<button class="btn" type="button" id="woolf">Волки</button>
					<button class="btn" type="button" id="mrakoris">Мракорис</button>
				</div>
			</div>

			<!-- Таверна -->
			<div class="box-item taverna border">
				<div class="npc_name"><b>Селина (Хозяйка таверны)</b></div>
				<div class="master_btn__box">
					<button class="btn" id="btn_toEat" type="button">Подкрепиться</button>
					<!-- <button class="btn" id="btn_rumors" type="button">Слухи</button> -->
					<button class="btn" id="btn_talkToSelina" type="button">Говорить</button>
                    <?php
                        // $('#btn_nagur')
                    ?>
					<button class="btn <?php if ($stage <= 5) echo 'dn'; else echo 'dib' ?>" id="btn_nagur" type="button">Нагур</button>
				</div>
				<div class="dialog_box db_1 bg-img selinaDB">
					<div class="db_close">х</div>
					<div class="DialogWithSelina"></div>
				</div>
			</div>

			<!-- Кузница -->
			<div class="box-item forge border">
				<div class="npc_name"><b>Харальд (Кузнец)</b></div>
				<div class="bg_inner bg_inner__forge">
					<label for="shortSword">
						<input id="shortSword" name="forgeItem" value="Доспех Ворона" type="radio"> Доспех Ворона - <span class="priceItemHero">1000</span> (Броня <em>20</em>)
					</label><br>
					<label for="dragonSword">
						<input id="dragonSword" name="forgeItem" value="Потрошитель Дракона" type="radio"> Фростморн - <span class="priceItemHero">800</span> (Урон <em>25</em>)
					</label>
				</div>
				<div class="npc harald"><img src="img/harald.png" alt=""></div>
				<div class="master_btn__box harald_btn">
					<button class="btn" id="btn_forge">Ковать</button>
					<button class="btn" type="button" id="HaraldProduct">Товары</button>
					<button class="btn" type="button" id="btn_talkToHarald">Говорить</button>
				</div>
				<div class="dialog_box db bg-img db_forge" id="db_forge">
					<div class="db_close">х</div>
					<div class="dinamicTxt"></div>
				</div>
			</div>

		</div>
	</div>

	<!-- Всплывающие окна ============== -->
	<div class="overlay"></div>
	<!-- Оповещения -->
	<!-- <div class="messWindow" id="messWindow">
		<div class="close">x</div>
		<p id="messWindowInner"></p>
		<span id="stop"></span>
		<span id="timeOfwork"></span>
	</div> -->
	<!-- Журнал -->
	<div class="journal_box messWindow">
		<div class="close">x</div>
		<p>Прогресс по сюжету:</p>
		<ul id="journal_box__inner"></ul>
	</div>
	<!-- Окно боя -->
	<div class="fight-box">
		<div class="fight-box__inner">
			<div class="fb_overlay"></div>
			<div class="hero_avatar" id="hero_avatar"><img src="img/avatar.png" alt=""></div>
			<div class="stat-box">
				<div class="stat-box_list stat-box__left">
					<ul>
						<li>Герой</li>
						<li><span>Сила: </span><span class="HeroPower"></span></li>
						<li><span>Урон: </span><span class="HeroDamage"></span></li>
						<li><span>Крит: </span><span class="HeroCrit"></span></li>
						<li><span>Броня: </span><span class="HeroArmor"></span></li>
						<li><span>Здоровье: </span><span class="HeroHP"></span></li>
					</ul>
				</div>
				<div class="stat-box_list stat-box__right">
					<ul>
						<li id="enemy_name"></li>
						<li><span>Мощь:</span> <span>???</span></li>
						<li><span>Урон:</span> <span>???</span></li>
						<li><span>Крит:</span> <span>???</span></li>
						<li><span>Крепость:</span> <span>???</span></li>
						<li><span>Здоровье:</span> <span>???</span></li>
					</ul>
				</div>
			</div>
			<div class="enemy_avatar" id="enemy_avatar"></div>
			<div class="dialog_box db db_fight" id="db_fight">
				<!-- <div class="db_close">х</div> -->
				<div class="dinamicTxt"></div>
			</div>
			<div class="fight-buttons">
				<button type="button" id="AtackToBattle">Атаковать</button>
				<button type="button" id="RetreatFromBattle">Отступить</button>
			</div>
		</div>
	</div>

	<!-- Онар -->
	<div class="OnarDialogBox">
		<div class="dialog_box db db-onar">
			<div class="dinamicTxt"></div>
		</div>
	</div>

	<!-- Туманная лощина -->
	<div class="BanditsDialogBox">
		<div class="dialog_box db db-bandits">
			<div class="dinamicTxt"></div>
		</div>
	</div>

	<!-- Развязка -->
	<div class="KillersDialogBox">
		<div class="dialog_box db db-killers">
			<div class="dinamicTxt"></div>
		</div>
	</div>

	<!-- Работа на ферме -->
	<div class="FarmWorker">
		<div class="messWindow" id="messWindow">
			<div class="close">x</div>
			<p id="messWindowInner"></p>
			<span id="stop"></span>
			<span id="timeOfwork"></span>
		</div>
	</div>

	<div class="messWindow HollowDB">
		<div class="dinamicTxt"></div>
	</div>

<!--    <script src="js/ajax_functions.js" defer></script>-->

    <script src="js/main.js"></script>
    <script src="js/main2.js"></script>


    <!-- Таймер -->
    <script src="js/timer.js"></script>

    <?php //echo "$js1"; ?>

    <h3 class="user_bottom_dev_caption dn">&copy; <span class="s_year"></span> All rights reserved
        <br>
        <span class="devs_span">Martin German && Aslan Bers@rk</span>
    </h3>


</body>

</html>
