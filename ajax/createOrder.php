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
global $USER;

// === Валидация обязательных полей ===
$errors = [];
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
if (empty($request['email']) || !preg_match($pattern, $request['email'])) $errors['email'] = "Введите корректный email";
if (empty($request['name'])) $errors['name'] = "Введите имя";
if (empty($request['surname'])) $errors['surname'] = "Введите фамилию";
if (empty($request['phone'])) $errors['phone'] = "Введите телефон";
if (empty($request['delivery'])) $errors['delivery'] = "Пожалуйста, выберите способ доставки.";
if (empty($request['city_cdek']) && empty($request['address_cdek'])) {
    $errors['delivery'] = 'Пожалуйста, выберите адрес.';
}
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

// === Инициализация ===
$siteId = $request['siteId'];
$fUserId = $request['fUserId'];

$utmSource = $request["utmSource"];
$utmCampaign = $request["utmCampaign"];
$utmPartner = $request["utmPartner"];

$email = $request["email"];
$phone = preg_replace("/[^0-9]/", "", $request["phone"]);
$name = $request["name"];
$surname = $request["surname"];
$comment = $request["comment"];
$delivery = $request["delivery"];
$deliveryPrice = (float)$request['delivery_price'] ?? 0;

if ($request["setBonus"] == 'Y') {
    $bonusPointsWithdraw = $request["bonus"];
} else {
    $bonusPointsWithdraw = 0;
    $bonusPoints = $request["bonusPoints"];
}

$basket = Basket::loadItemsForFUser($fUserId, $siteId);

// == cdek ==
require_once $_SERVER['DOCUMENT_ROOT'] . '/ajax/cdek/create_cdek_order.php';
$cdek = $request['cdek'];
$city_cdek = $request['city_cdek'];
$city_code_cdek = $request['city_code_cdek'];
$tariff_cdek = $request['tariff_cdek'];
$address_cdek = $request['address_cdek'];
$pvz_code_cdek = $request['pvz_code_cdek'];
$postal_code_cdek = $request['postal_code_cdek'];
$formatted_cdek = $request['formatted_cdek'];
$promo = trim($request['promo'] ?? '');
// ===== ПРОМОКОД =====
DiscountCouponsManager::clear(true);
if ($promo) {
    DiscountCouponsManager::add($promo);
}
$basket->refreshData(['PRICE', 'COUPONS']);
$order = Order::create($siteId, $USER->GetID() ?: 44);
$order->setBasket($basket);

$discounts = $order->getDiscount();
$discounts->calculate();
$order->doFinalAction(true);

$totalPrice = $basket->getPrice() - (float)$bonusPointsWithdraw + $deliveryPrice;


// =========================================
// === Оплата онлайн TBANK ===
// =========================================
if ($request["payment"] === 'card') {

    // создаём виртуальный заказ
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
        if ($key == 0 && $deliveryPrice > 0) {
            $itemPrice += $deliveryPrice * 100;
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
            'promocode' => $promo,
            'utmSource' => $utmSource,
            'utmCampaign' => $utmCampaign,
            'utmPartner' => $utmPartner,

            'cdek' => $request['cdek'],
            'city_cdek' => $request['city_cdek'],
            'city_code_cdek' => $request['city_code_cdek'],
            'tariff_cdek' => $request['tariff_cdek'],
            'address_cdek' => $request['address_cdek'],
            'pvz_code_cdek' => $request['pvz_code_cdek'],
            'postal_code_cdek' => $request['postal_code_cdek'],
            'deliveryPrice' => $deliveryPrice,
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
// === Оплата онлайн АЛЬФА ===
// =========================================
if ($request["payment"] === 'card_') {
    require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/alfa_auth.php';
    $payKeeper = new PayKeeper(
        'https://vl28.server.paykeeper.ru',
        $login,
        $password
    );
    $orderTempId = uniqid('vl28_', true);
    try {
        $token = $payKeeper->getToken();

        $resultData = $payKeeper->createInvoice([
            'pay_amount' => number_format($totalPrice, 2, '.', ''),
            'clientid' => trim($surname . ' ' . $name),
            'orderid' => $orderTempId,
            'service_name' => 'Заказ №' . $orderTempId,
            'client_email' => $email,
            'client_phone' => '+' . $phone,
        ]);

        $payUrl = $resultData['invoice_url'] ?? '';

    } catch (Exception $e) {

        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);

        exit();
    }

    $fields = [
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
        'promocode' => $promo,
        'utmSource' => $utmSource,
        'utmCampaign' => $utmCampaign,
        'utmPartner' => $utmPartner,

        'cdek' => $request['cdek'],
        'city_cdek' => $request['city_cdek'],
        'city_code_cdek' => $request['city_code_cdek'],
        'tariff_cdek' => $request['tariff_cdek'],
        'address_cdek' => $request['address_cdek'],
        'pvz_code_cdek' => $request['pvz_code_cdek'],
        'postal_code_cdek' => $request['postal_code_cdek'],
        'deliveryPrice' => $deliveryPrice,
    ];
    try {
        setHLData(
            'PendingPayments',
            [
                'UF_ORDER_ID' => $orderTempId,
                'UF_DATA' => json_encode($fields, JSON_UNESCAPED_UNICODE),
                'UF_STATUS' => 'NEW',
                'UF_CREATED' => date('d.m.Y H:i:s'),
            ]
        );
    } catch (Exception $e) {
        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);

        exit();
    }

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'success',
        'message' => 'Перенаправление на оплату',
        'pay_url' => $payUrl,
        'tmp_order_id' => $orderTempId,
    ]);

    exit();
}