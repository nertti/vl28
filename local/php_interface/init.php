<?php
// ПОДКЛЮЧАЕМ ФУНКЦИИ
if(file_exists($_SERVER['DOCUMENT_ROOT']. "/local/php_interface/include/functions.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/functions.php");
// Подключаем класс события полной оплаты заказа
if(file_exists($_SERVER['DOCUMENT_ROOT']. "/local/php_interface/handlers/OrderHandler.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/handlers/OrderHandler.php");
// ПОДКЛЮЧАЕМ СОБЫТИЯ
if(file_exists($_SERVER['DOCUMENT_ROOT']. "/local/php_interface/events.php"))
    require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/events.php");
//запомнить авторизацию
if(file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/classes/RememberAuth.php"))
    require_once ($_SERVER['DOCUMENT_ROOT']."/local/php_interface/classes/RememberAuth.php");