<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03.05.2019
 * Time: 12:24
 */

//CREATE DATABASE `warmaster1` CHARACTER SET `utf8` COLLATE `utf8_general_ci`;
//CREATE USER 'warmaster1'@'localhost' IDENTIFIED BY '1';
//GRANT ALL PRIVILEGES ON `warmaster1`.* TO 'warmaster1'@'localhost';
//FLUSH PRIVILEGES;

//REVOKE ALL PRIVILEGES on `warmaster1`.* FROM `warmaster1`@'localhost'
//DROP USER if exists 'warmaster1'@'localhost';
//drop database if exists `warmaster1`;
//FLUSH PRIVILEGES;

define("HOST", "localhost");
define("USER", "warmaster1");
define("PASSWORD", "1");
define("DBNAME", "warmaster1");
define("MYSQL_PORT", 3309);
define("CHARSET", "utf8");
define("MYSQL_SALT", "web_@DEV_112G1b11@g");