<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\UserAccountTable;

/** @global CMain $APPLICATION */

global $USER;

$userBonus = 0;

if (!Loader::includeModule("sale")) {
    echo "Модуль Интернет-магазина (sale) не установлен";
    return;
}

if ($USER->IsAuthorized()) {
    $userId = $USER->GetID();
    $userBalance = CSaleUserAccount::GetByUserID($userId, "RUB");
    $userBonus = number_format($userBalance['CURRENT_BUDGET'], 0,'.',' ') ?: null;

}
