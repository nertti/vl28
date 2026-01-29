<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
session_start();

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;

Loader::includeModule("sale");
Loader::includeModule("catalog");

$request = Context::getCurrent()->getRequest();

// === Валидация ===
$errors = [];
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
if (empty($request['email']) || !preg_match($pattern, $request['email'])) $errors['email'] = "Введите корректный email";
if (empty($request['name'])) $errors['name'] = "Введите имя";
if (empty($request['surname'])) $errors['surname'] = "Введите фамилию";
if (empty($request['phone'])) $errors['phone'] = "Введите телефон";
if (empty($request['delivery'])) {
    $errors['delivery'] = "Пожалуйста, выберите способ доставки.";
}
if ($request['delivery'] == 135) {
    if (empty($request['street'])) $errors['street'] = "Укажите улицу.";
    if (empty($request['dom'])) $errors['dom'] = "Укажите номер дома.";
    if (empty($request['kvartira'])) $errors['kvartira'] = "Укажите номер квартиры.";
}
//if ($request['delivery'] == 137 || $request['delivery'] == 139) {
//    if (empty($request['city'])) $errors['city'] = "Укажите населённый пункт.";
//}
//if ($request['delivery'] == 136 || $request['delivery'] == 138) {
//    if (empty($request['city'])) $errors['city'] = "Укажите населённый пункт.";
//    if (empty($request['street'])) $errors['street'] = "Укажите улицу.";
//    if (empty($request['dom'])) $errors['dom'] = "Укажите номер дома.";
//    if (empty($request['kvartira'])) $errors['kvartira'] = "Укажите номер квартиры.";
//}
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

global $USER;
$email = $request["email"];
$phone = preg_replace("/[^0-9]/", "", $request["phone"]);
$name = $request["name"];
$surname = $request["surname"];
$comment = $request["comment"];
if ($request["setBonus"] == 'Y') {
    $bonusPointsWithdraw = $request["bonus"];
}
$bonusPoints = $request["bonusPoints"];
if ($request["delivery"] == 135) {
    $city = "Москва";
} else {
    $city = $request["city"];
}
$street = $request["street"];
$dom = $request["dom"];
$kvartira = $request["kvartira"];
$siteId = $request['siteId'];
$fUserId = $request['fUserId'];
$basket = Basket::loadItemsForFUser($fUserId, $siteId);
$totalPrice = $basket->getPrice() - (float)$bonusPointsWithdraw;
$deliveryPrice = (float)$request['delivery_price'] ?? 300;

// =========================================
// === Вариант 1: Оплата онлайн (CARD) ===
// =========================================
if ($request["payment"] === 'card') {

    require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/t_auth.php';
    $apiUrl = 'https://securepay.tinkoff.ru/v2/Init';
    $orderTempId = uniqid('vl28_', true); // временный ID

    $amount = intval(round($totalPrice * 100));
    $description = 'Предоплата заказа №' . $orderTempId;

    $tokenFields = [
        'TerminalKey' => $terminalKey,
        'Amount' => $amount,
        'OrderId' => $orderTempId,
        'Description' => $description,
        'Password' => $secretKey,
		'NotificationURL' => 'https://vl28.pro/ajax/paySuccess.php',
    ];
    ksort($tokenFields);
    $token = hash('sha256', implode('', $tokenFields));

    // Формируем запрос к Tinkoff
    $requestData = [
        'TerminalKey' => $terminalKey,
        'Amount' => $amount,
        'OrderId' => $orderTempId,
        'Description' => $description,
        'Token' => $token,
		'NotificationURL' => 'https://vl28.pro/ajax/paySuccess.php',
        'DATA' => [
            'Phone' => '+' . $phone,
            'Email' => $email,
        ],
        'Receipt' => [
            'Email' => $email,
            'Phone' => '+' . $phone,
            'Taxation' => 'osn',
            'Items' => [],
        ],
    ];
    foreach ($basket as $key => $basketItem) {
        $itemPrice = intval(round($basketItem->getPrice() * 100));
        if ($key == 0 && $bonusPointsWithdraw > 0) {
            $itemPrice -= $bonusPointsWithdraw * 100;
        }
        $itemQuantity = $basketItem->getQuantity();
        $requestData['Receipt']['Items'][] = [
            'Name' => $basketItem->getField('NAME'),
            'Price' => $itemPrice,
            'Quantity' => $itemQuantity,
            'Amount' => $itemPrice * $itemQuantity,
            'Tax' => 'vat10',
        ];
    }

    // Отправляем CURL
    $curl = curl_init($apiUrl);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $resultData = json_decode($response, true);
    $payUrl = $resultData['PaymentURL'] ?? '';

    // сохраняем данные для последующего создания заказа после успешной оплаты
    $_SESSION['PENDING_ORDER'][$orderTempId] = [
        'FIELDS' => [
            'email' => $email,
            'phone' => $phone,
            'name' => $name,
            'surname' => $surname,
            'comment' => $comment,
            'siteId' => $siteId,
            'fUserId' => $fUserId,
            'delivery' => $request['delivery'],
            'city' => $request["city"],
            'street' => $request["street"],
            'dom' => $request["dom"],
            'kvartira' => $request["kvartira"],
            'bonusPoints' => empty($bonusPointsWithdraw) ? $bonusPoints : 0,
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode([
        'status' => $payUrl ? 'success' : 'error',
        'message' => $payUrl ? 'Перенаправление на оплату' : 'Ошибка инициализации платежа',
        'pay_url' => $payUrl,
        'tmp_order_id' => $orderTempId,
        'requestData' => $requestData,
    ]);
    exit();
}


// =========================================
// === Вариант 2: Другие оплаты (создаём заказ сразу) ===
// =========================================

$order = Order::create($siteId, $USER->GetID() ?: 44);
$order->setPersonTypeId(1);
$order->setField('USER_DESCRIPTION', $comment);
$order->setBasket($basket);

// Доставка
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$service = Delivery\Services\Manager::getById($request["delivery"]);
$shipment->setFields([
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
    'BASE_PRICE_DELIVERY' => $deliveryPrice,
    'PRICE_DELIVERY' => $deliveryPrice,
    'CUSTOM_PRICE_DELIVERY' => 'Y',
]);

// Оплата
$paymentCollection = $order->getPaymentCollection();
$paySystemService = PaySystem\Manager::getObjectById(1);
if ($bonusPointsWithdraw > 0) {
    // Первый платеж - бонусные баллы
    $payment = $paymentCollection->createItem();
    $payment->setFields([
        'PAY_SYSTEM_ID' => 6,
        'PAY_SYSTEM_NAME' => PaySystem\Manager::getObjectById(6)->getField("NAME"),
        'SUM' => $bonusPointsWithdraw,
    ]);
    $payment->setField('PAID', 'Y');
    // Второй платеж
    $remainingSum = $order->getPrice() - $bonusPointsWithdraw;
    if ($remainingSum > 0) {
        $newPayment = $paymentCollection->createItem();
        $newPayment->setFields([
            'PAY_SYSTEM_ID' => 1,
            'PAY_SYSTEM_NAME' => PaySystem\Manager::getObjectById(1)->getField("NAME"),
            'SUM' => $remainingSum,
        ]);
    }
} else {
    // Единый платеж
    $payment = $paymentCollection->createItem();
    $payment->setFields([
        'PAY_SYSTEM_ID' => 1,
        'PAY_SYSTEM_NAME' => PaySystem\Manager::getObjectById(1)->getField("NAME"),
        'SUM' => $order->getPrice(),
    ]);
}

// Заполняем свойства
$propertyCollection = $order->getPropertyCollection();
$propertyCollection->getItemByOrderPropertyCode('EMAIL')->setValue($email);
$propertyCollection->getItemByOrderPropertyCode('PHONE')->setValue($phone);
$propertyCollection->getItemByOrderPropertyCode('NAME')->setValue($name);
$propertyCollection->getItemByOrderPropertyCode('SURNAME')->setValue($surname);
$propertyCollection->getItemByOrderPropertyCode('CITY')->setValue($city);
$propertyCollection->getItemByOrderPropertyCode('STREET')->setValue($street);
$propertyCollection->getItemByOrderPropertyCode('HOUSE')->setValue($dom);
$propertyCollection->getItemByOrderPropertyCode('APARTMENT')->setValue($kvartira);
$propertyCollection->getItemByOrderPropertyCode('BONUS')->setValue(empty($bonusPointsWithdraw) ? $bonusPoints : 0);

$order->doFinalAction(true);
$result = $order->save();

header('Content-Type: application/json');
if ($result->isSuccess()) {
    echo json_encode(['status' => 'success', 'message' => 'Заказ успешно создан']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка сохранения заказа']);
}
