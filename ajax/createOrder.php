<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
session_start();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\DiscountCouponsManager;

Loader::includeModule("sale");
Loader::includeModule("catalog");

$request = Context::getCurrent()->getRequest();

// ================= ВАЛИДАЦИЯ =================

$errors = [];
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

if (empty($request['email']) || !preg_match($pattern, $request['email'])) $errors['email'] = "Введите корректный email";
if (empty($request['name'])) $errors['name'] = "Введите имя";
if (empty($request['surname'])) $errors['surname'] = "Введите фамилию";
if (empty($request['phone'])) $errors['phone'] = "Введите телефон";
if (empty($request['delivery'])) $errors['delivery'] = "Пожалуйста, выберите способ доставки.";

if ($request['delivery'] == 135) {
    if (empty($request['street'])) $errors['street'] = "Укажите улицу.";
    if (empty($request['dom'])) $errors['dom'] = "Укажите номер дома.";
    if (empty($request['kvartira'])) $errors['kvartira'] = "Укажите номер квартиры.";
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

// ================= ДАННЫЕ =================

global $USER;

$email = $request["email"];
$phone = preg_replace("/[^0-9]/", "", $request["phone"]);
$name = $request["name"];
$surname = $request["surname"];
$comment = $request["comment"];

$promoCode = trim($request['promo'] ?? '');

$bonusPointsWithdraw = ($request["setBonus"] == 'Y') ? (float)$request["bonus"] : 0;
$bonusPoints = $request["bonusPoints"];

$city = ($request["delivery"] == 135) ? "Москва" : $request["city"];
$street = $request["street"];
$dom = $request["dom"];
$kvartira = $request["kvartira"];

$siteId = $request['siteId'];
$fUserId = $request['fUserId'];

$basket = Basket::loadItemsForFUser($fUserId, $siteId);

$deliveryPrice = (float)$request['delivery_price'] ?? 0;

// ================= ПРОМОКОД =================

DiscountCouponsManager::init();
DiscountCouponsManager::clear(true);

if (!empty($promoCode)) {
    DiscountCouponsManager::add($promoCode);
}

// ================= ФУНКЦИЯ СОЗДАНИЯ ЗАКАЗА =================

function buildOrder($siteId, $userId, $basket, $deliveryId, $deliveryPrice, $comment)
{
    $order = Order::create($siteId, $userId);
    $order->setPersonTypeId(1);
    $order->setField('USER_DESCRIPTION', $comment);
    $order->setBasket($basket);

    // Доставка
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $service = Delivery\Services\Manager::getById($deliveryId);

    $shipment->setFields([
        'DELIVERY_ID' => $service['ID'],
        'DELIVERY_NAME' => $service['NAME'],
        'BASE_PRICE_DELIVERY' => $deliveryPrice,
        'PRICE_DELIVERY' => $deliveryPrice,
        'CUSTOM_PRICE_DELIVERY' => 'Y',
    ]);

    return $order;
}

// ================= ONLINE ОПЛАТА =================

if ($request["payment"] === 'card') {

    $orderTmp = buildOrder($siteId, $USER->GetID() ?: 44, $basket, $request["delivery"], $deliveryPrice, $comment);

    $orderTmp->doFinalAction(true);

    // Проверяем промокод
    if (!empty($promoCode)) {
        $coupons = DiscountCouponsManager::get(true);
        $applied = false;

        foreach ($coupons as $coupon) {
            if ($coupon['COUPON'] === $promoCode &&
                $coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
                $applied = true;
            }
        }

        if (!$applied) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Промокод не применился'
            ]);
            exit();
        }
    }

    $totalPrice = $orderTmp->getPrice() - $bonusPointsWithdraw;

    require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/t_auth.php';

    $orderTempId = uniqid('vl28_', true);
    $amount = intval(round($totalPrice * 100));

    // тут твой код Tinkoff без изменений...

    $_SESSION['PENDING_ORDER'][$orderTempId] = [
        'FIELDS' => $request->toArray()
    ];

    echo json_encode([
        'status' => 'success',
        'pay_url' => $payUrl ?? ''
    ]);
    exit();
}

// ================= СОЗДАНИЕ ЗАКАЗА =================

$order = buildOrder($siteId, $USER->GetID() ?: 44, $basket, $request["delivery"], $deliveryPrice, $comment);

// Применяем скидки
$order->doFinalAction(true);

// Проверка промокода
if (!empty($promoCode)) {
    $coupons = DiscountCouponsManager::get(true);
    $applied = false;

    foreach ($coupons as $coupon) {
        if ($coupon['COUPON'] === $promoCode &&
            $coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
            $applied = true;
        }
    }

    if (!$applied) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Промокод не применился'
        ]);
        exit();
    }
}

// ================= ОПЛАТА =================

$paymentCollection = $order->getPaymentCollection();

if ($bonusPointsWithdraw > 0) {

    $payment = $paymentCollection->createItem();
    $payment->setFields([
        'PAY_SYSTEM_ID' => 6,
        'SUM' => $bonusPointsWithdraw,
    ]);
    $payment->setField('PAID', 'Y');

    $remainingSum = $order->getPrice() - $bonusPointsWithdraw;

    if ($remainingSum > 0) {
        $newPayment = $paymentCollection->createItem();
        $newPayment->setFields([
            'PAY_SYSTEM_ID' => 1,
            'SUM' => $remainingSum,
        ]);
    }

} else {
    $payment = $paymentCollection->createItem();
    $payment->setFields([
        'PAY_SYSTEM_ID' => 1,
        'SUM' => $order->getPrice(),
    ]);
}

// ================= СВОЙСТВА =================

$propertyCollection = $order->getPropertyCollection();

$propertyCollection->getItemByOrderPropertyCode('EMAIL')->setValue($email);
$propertyCollection->getItemByOrderPropertyCode('PHONE')->setValue($phone);
$propertyCollection->getItemByOrderPropertyCode('NAME')->setValue($name);
$propertyCollection->getItemByOrderPropertyCode('SURNAME')->setValue($surname);
$propertyCollection->getItemByOrderPropertyCode('CITY')->setValue($city);
$propertyCollection->getItemByOrderPropertyCode('STREET')->setValue($street);
$propertyCollection->getItemByOrderPropertyCode('HOUSE')->setValue($dom);
$propertyCollection->getItemByOrderPropertyCode('APARTMENT')->setValue($kvartira);

// ================= СОХРАНЕНИЕ =================

$result = $order->save();

if ($result->isSuccess()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Заказ успешно создан'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => implode(', ', $result->getErrorMessages())
    ]);
}
