<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\DiscountCouponsManager;

header('Content-Type: application/json');

Loader::includeModule('sale');
Loader::includeModule('catalog');

global $USER;

$data = json_decode(file_get_contents('php://input'), true);

$promo = trim($data['promo'] ?? '');
$bonusRequest = (int)($data['bonus'] ?? 0);
$deliveryPrice = (float)($data['delivery_price'] ?? 0);

// безопасность
$fUserId = Sale\Fuser::getId();
$siteId = SITE_ID;
$userId = $USER->IsAuthorized() ? $USER->GetID() : 0;

// корзина
$basket = Sale\Basket::loadItemsForFUser($fUserId, $siteId);

// создаём заказ
$order = Sale\Order::create($siteId, $userId);
$order->setPersonTypeId(1);
$order->setBasket($basket);

// ===== ПРОМОКОД =====
DiscountCouponsManager::clear(true);
if ($promo) {
    DiscountCouponsManager::add($promo);
}

// ===== ДОСТАВКА =====
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();

$shipment->setFields([
    'DELIVERY_ID' => 1, // подставь нужный
    'DELIVERY_NAME' => 'Доставка',
    'BASE_PRICE_DELIVERY' => $deliveryPrice,
    'PRICE_DELIVERY' => $deliveryPrice,
    'CUSTOM_PRICE_DELIVERY' => 'Y',
]);

// ===== ПЕРВЫЙ РАСЧЁТ =====
$order->doFinalAction(true);

$basePrice = $basket->getBasePrice();
$basketPrice = $basket->getPrice();
$discount = $basePrice - $basketPrice;

// ===== БОНУСЫ (ПРАВИЛЬНО) =====

// получаем реальные бонусы пользователя
$userBonus = 0;
if ($USER->IsAuthorized()) {
    require_once $_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php";
    $userBonus = (int)$userBonus;
}

// максимум списания (например 90%)
$maxBonus = min($userBonus, floor($basketPrice * 0.9));

$bonusUsed = min($bonusRequest, $maxBonus);

// итог
$total = $basketPrice + $deliveryPrice - $bonusUsed;
$total = max($total, 0);

// бонусы к начислению
$percent = 5; // или из настроек
$bonusEarn = $bonusUsed > 0 ? 0 : floor($total * $percent / 100);

// ===== ОТВЕТ =====
echo json_encode([
    'status' => 'success',
    'total' => $total,
    'basket_price' => $basketPrice,
    'discount' => $discount,
    'delivery' => $deliveryPrice,
    'bonus_used' => $bonusUsed,
    'bonus_available' => $userBonus,
    'bonus_max' => $maxBonus,
    'bonus_earn' => $bonusEarn,
]);