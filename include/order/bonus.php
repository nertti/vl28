<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/** @global CMain $APPLICATION */

global $USER;
CModule::IncludeModule('logictim.balls');
$userBonus = 0;
$userBonus = cHelper::UserBallance($USER->GetID()); // возвращает бонусы у текущего пользователя

//pr($userBonus, true);