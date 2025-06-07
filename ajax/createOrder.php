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
    // === ЗАПРОС В Т-БАНК НА ОПЛАТУ ===
    $apiUrl = 'https://securepay.tinkoff.ru/v2/Init'; // URL для создания платежа
    $terminalKey = '1713425997317';
    $secretKey = '1nujjyr9acqxgf9i';

    // Сумма в копейках
    $amount = intval(round($price * 100)); // например 1234.56 => 123456

    // Подпись (Token)
    $tokenData = [
        'Amount' => $amount,
        'OrderId' => $orderId,
        'Password' => $secretKey,
        'TerminalKey' => $terminalKey,
    ];
    ksort($tokenData);
    $tokenString = '';
    foreach ($tokenData as $value) {
        $tokenString .= $value;
    }
    $token = hash('sha256', $tokenString);

    // Данные запроса
    $requestData = [
        'TerminalKey' => $terminalKey,
        'Amount' => $amount,
        'OrderId' => $orderId,
        'Description' => 'Оплата заказа №' . $orderId,
        'Token' => $token,
        'Receipt' => [
            'Email' => $email,
            'Taxation' => 'osn',
            'Items' => [],
        ],
    ];

    // Заполнение данных товаров для чека
    foreach ($basket as $basketItem) {
        $itemPrice = intval(round($basketItem->getPrice() * 100));
        $itemQuantity = $basketItem->getQuantity();
        $requestData['Receipt']['Items'][] = [
            'Name' => $basketItem->getField('NAME'),
            'Price' => $itemPrice,
            'Quantity' => $itemQuantity,
            'Amount' => $itemPrice * $itemQuantity,
            'Tax' => 'vat10',
        ];
    }

    // Отправка запроса в Т-Банк
    $curl = curl_init($apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
    $response = curl_exec($curl);
    curl_close($curl);

    $resultData = json_decode($response, true);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($resultData, 1), FILE_APPEND);
    if (!empty($resultData['PaymentURL'])) {
        $payUrl = $resultData['PaymentURL'];
    } else {
        $payUrl = null;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'Заказ успешно оформлен',
        'price' => $price,
        'order_id' => $orderId,
        'pay_url' => $payUrl
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $result]);
}
