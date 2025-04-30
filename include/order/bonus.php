<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/** @global CMain $APPLICATION */

global $USER;
use Bitrix\Main\Loader;
Loader::includeModule("logictim.balls");
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/logictim.balls/classes/module_sale_16/cHelper.php";
$userBonus = 0;

$userBonus = cHelper::UserBallance($USER->GetID()); // возвращает бонусы у текущего пользователя
//pr($userBonus, true);