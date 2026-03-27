<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Sale;
use Bitrix\Sale\DiscountCouponsManager;

header('Content-Type: application/json');

Loader::includeModule('sale');
Loader::includeModule('catalog');

$request = Application::getInstance()->getContext()->getRequest();
$data = json_decode(file_get_contents('php://input'), true);

$promoCode = trim($data['promo'] ?? '');

if (!$promoCode) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Введите промокод'
    ]);
    exit;
}

// =======================
// Инициализация корзины
// =======================

$siteId = SITE_ID;
$fUserId = Sale\Fuser::getId();
$basket = Sale\Basket::loadItemsForFUser($fUserId, $siteId);

if ($basket->isEmpty()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Корзина пуста'
    ]);
    exit;
}

// =======================
// Сбрасываем купоны
// =======================

DiscountCouponsManager::clear(true);

// =======================
// Добавляем купон
// =======================

$addResult = DiscountCouponsManager::add($promoCode);

if (!$addResult) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Промокод не существует'
    ]);
    exit;
}

// =======================
// Создаём заказ (для расчёта)
// =======================

$order = Sale\Order::create($siteId, $fUserId);
$order->setBasket($basket);

// Обязательно — иначе скидки не применятся
$discount = $order->getDiscount();
$discount->calculate();

// =======================
// Получаем результат
// =======================

$discountData = $discount->getApplyResult(true);

$discountSum = 0;

// Суммируем все скидки
if (!empty($discountData['PRICES']['BASKET'])) {
    foreach ($discountData['PRICES']['BASKET'] as $item) {
        if (isset($item['DISCOUNT'])) {
            $discountSum += $item['DISCOUNT'];
        }
    }
}

// Проверяем применился ли купон
$coupons = DiscountCouponsManager::get(true);

$applied = false;

foreach ($coupons as $coupon) {
    if ($coupon['COUPON'] === $promoCode && $coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
        $applied = true;
    }
}

// =======================
// Ответ
// =======================

if (!$applied) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Промокод не применился (условия не выполнены)'
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'discount' => round($discountSum)
]);