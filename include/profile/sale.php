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

if ($totalPaid >= 150000) {
    $discountPercent = 15;
    $discountCard = 'Luxury';
} elseif ($totalPaid >= 75000) {
    $discountPercent = 10;
    $discountCard = 'Highlight';
} else {
    $discountPercent = 5;
    $discountCard = 'Light';
}