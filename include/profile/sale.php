<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale;

Loader::includeModule("sale");

global $USER;
$userId = $USER->GetID();

$totalPaid = 0;

// Получаем оплаченные заказы пользователя
$dbRes = Sale\Order::getList([
    'filter' => [
        'USER_ID' => $userId,
        'PAYED' => 'Y',
    ],
    'select' => ['PRICE']
]);

while ($order = $dbRes->fetch()) {
    $totalPaid += $order['PRICE'];
}
$discountPercent = 0;

$arSelect = Array(
    'ID',
    'IBLOCK_ID',
    'NAME',
    'PROPERTY_CONDITIONS',
    'PROPERTY_BONUS',
);
$arFilter = Array("IBLOCK_ID"=>IntVal(21), "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
$arCards = [];
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arCards[] = $arFields;
}


if ($totalPaid < $arCards[1]['PROPERTY_CONDITIONS_VALUE']) {
    $discountPercent = $arCards[0]['PROPERTY_BONUS_VALUE'];
    $discountCard = $arCards[0]['NAME'];
} elseif ($totalPaid < $arCards[2]['PROPERTY_CONDITIONS_VALUE']) {
    $discountPercent = $arCards[1]['PROPERTY_BONUS_VALUE'];
    $discountCard = $arCards[1]['NAME'];
} else {
    $discountPercent = $arCards[2]['PROPERTY_BONUS_VALUE'];
    $discountCard = $arCards[2]['NAME'];
}