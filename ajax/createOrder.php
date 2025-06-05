<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;

global $USER;

Loader::includeModule("sale");
Loader::includeModule("catalog");

$request = Context::getCurrent()->getRequest();
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

$errors = [];
if (empty($request['email'])) {
    $errors['email'] = "Пожалуйста, введите свой email.";
} elseif (!preg_match($pattern, $request['email'])) {
    $errors['email'] = "Введите корректный электронный адрес";
}
if (empty($request['name'])) {
    $errors['name'] = "Пожалуйста, введите своё имя.";
}
if (empty($request['surname'])) {
    $errors['surname'] = "Пожалуйста, введите свою фамилию.";
}
if (empty($request['phone'])) {
    $errors['phone'] = "Пожалуйста, введите свой телефон.";
}
if ($request['delivery'] == 1 || $request['delivery'] == 3) {
    if (empty($request['city'])) $errors['city'] = "Пожалуйста, укажите населённый пункт.";
    if (empty($request['street'])) $errors['street'] = "Пожалуйста, укажите улицу.";
    if (empty($request['dom'])) $errors['dom'] = "Пожалуйста, укажите номер дома.";
    if (empty($request['kvartira'])) $errors['kvartira'] = "Пожалуйста, укажите номер квартиры.";
}
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

$phone = $request["phone"];
$phoneCleaned = preg_replace("/[^0-9]/", "", $_POST["phone"]);
$name = $request["name"];
$comment = $request["comment"];
$email = $request["email"];

$fUserId = $request['fUserId'];
$siteId = $request['siteId'];
$basket = Basket::loadItemsForFUser($fUserId, $siteId);

$userId = $USER->GetID();

if (!$USER->isAuthorized()) {
    $rsUsers = CUser::GetList(array(), 'sort', array('PERSONAL_PHONE' => $phoneCleaned));
    if ($rsUsers->SelectedRowsCount() <= 0) {
        $arResult = $USER->Register($phoneCleaned, "", "", $phoneCleaned, $phoneCleaned, $phoneCleaned . "@vl28.ru");
        if ($arResult['TYPE'] == 'OK') {
            $fields = array("PERSONAL_PHONE" => $phoneCleaned);
            $USER->Update($arResult['ID'], $fields);
            $userId = $USER->GetID();
        }
    } else {
        $rsUser = CUser::GetByLogin($phoneCleaned);
        $arUser = $rsUser->Fetch();
        $userId = $arUser['ID'];
    }
    $USER->Logout();
}

// Создание заказа
$order = Order::create($siteId, $USER->isAuthorized() ? $USER->GetID() : $userId);
$order->setPersonTypeId(1);
if ($comment) {
    $order->setField('USER_DESCRIPTION', $comment);
}
$order->setBasket($basket);

// Отгрузка
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
$shipment->setFields(array(
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
));
$shipmentItemCollection = $shipment->getShipmentItemCollection();

// Оплата
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem();
$paySystemService = PaySystem\Manager::getObjectById(7); //"Т-Банк"
$payment->setFields(array(
    'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
    'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
));

$order->doFinalAction(true);
$result = $order->save();
$orderId = $order->getId();
$price = $order->getPrice();

if ($result->isSuccess()) {

    // 📦 Интеграция с Т-Банком
    $terminalKey = '1713425997317';
    $secretKey = '1nujjyr9acqgxf9i';
    $amountKopecks = intval($price * 100); // копейки
    $orderNumber = 'ORDER-' . $orderId;

    // Токен по документации Т-Банка (в алфавитном порядке ключей)
    $tokenData = [
        'Amount' => $amountKopecks,
        'Description' => 'Оплата заказа №' . $orderId,
        'OrderId' => $orderNumber,
        'TerminalKey' => $terminalKey,
        'DATA' => [
            'Email' => $email,
            'Phone' => $phoneCleaned
        ]
    ];
    $flattened = array_merge($tokenData, ['Password' => $secretKey]);
    ksort($flattened);
    $tokenString = '';
    foreach ($flattened as $k => $v) {
        if (is_array($v)) continue;
        $tokenString .= $v;
    }
    $token = hash('sha256', $tokenString);

    $requestData = [
        'TerminalKey' => $terminalKey,
        'Amount' => $amountKopecks,
        'OrderId' => $orderNumber,
        'Description' => 'Оплата заказа №' . $orderId,
        //'SuccessURL' => 'https://ваш-домен.рф/payment-success.php',
        //'FailURL' => 'https://ваш-домен.рф/payment-fail.php',
        //'NotificationURL' => 'https://ваш-домен.рф/api/tinkoff-callback.php',
        'DATA' => [
            'Email' => $email,
            'Phone' => $phoneCleaned
        ],
        'Token' => $token
    ];

    $ch = curl_init('https://securepay.tinkoff.ru/v2/Init');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (!empty($responseData['PaymentURL'])) {
        $payUrl = $responseData['PaymentURL'];
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Заказ успешно оформлен',
        'price' => $price,
        'order_id' => $orderId,
        'pay_url' => $payUrl ?? null
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $result]);
}
