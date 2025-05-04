<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/** @global CMain $APPLICATION */

global $USER;

$userBonus = 0;
$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();
$userBonus = $arUser['UF_BONUS'];
//pr($arUser, true);