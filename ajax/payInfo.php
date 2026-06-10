<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;

Loader::includeModule('sale');
Loader::includeModule('catalog');
Loader::includeModule('highloadblock');

require_once $_SERVER['DOCUMENT_ROOT'] . '/ajax/cdek/create_cdek_order.php';


$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$postData = $request->getPostList()->toArray();


$orderTempId = $postData['orderid'] ?? '';
$paymentId = $postData['invoice_id'] ?? '';
$amount = (float)($postData['sum'] ?? 0);

if (empty($orderTempId)) {
    http_response_code(400);
    die('ORDER_ID_EMPTY');
}

/**
 * Ищем временный заказ
 */
$pendingRows = getHLData(
    'PendingPayments',
    [
        '=UF_ORDER_ID' => $orderTempId
    ]
);

if (empty($pendingRows)) {
    http_response_code(404);
    die('ORDER_NOT_FOUND');
}

$pendingOrder = $pendingRows[0];

/**
 * Защита от повторного callback
 */
if ($pendingOrder['UF_STATUS'] === 'PAID') {
    die('OK');
}

$fields = json_decode($pendingOrder['UF_DATA'], true);

if (empty($fields)) {
    http_response_code(500);
    die('ORDER_DATA_EMPTY');
}

global $USER;

$siteId = $fields['siteId'];
$fUserId = $fields['fUserId'];

$userId = $pendingOrder['UF_USER_ID'];

/**
 * Корзина
 */
$basket = Basket::loadItemsForFUser(
    $fUserId,
    $siteId
);

if ($basket->isEmpty()) {
    http_response_code(500);
    die('BASKET_EMPTY');
}

/**
 * Создание заказа
 */
$order = Order::create($siteId, $userId);
$order->setBasket($basket);

$discounts = $order->getDiscount();
$discounts->calculate();

$order->setPersonTypeId(1);
$order->setField('USER_DESCRIPTION', $fields['comment']);

$order->doFinalAction(true);

/**
 * Доставка
 */
$shipmentCollection = $order->getShipmentCollection();

$shipment = $shipmentCollection->createItem();

$shipmentItemCollection = $shipment->getShipmentItemCollection();

foreach ($basket as $basketItem) {

    $shipmentItem = $shipmentItemCollection->createItem($basketItem);

    $shipmentItem->setQuantity($basketItem->getQuantity());
}

$service = Delivery\Services\Manager::getById(
    $fields['delivery'] ?? 1
);

$shipment->setFields([
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
    'BASE_PRICE_DELIVERY' => $fields['deliveryPrice'],
    'PRICE_DELIVERY' => $fields['deliveryPrice'],
    'CUSTOM_PRICE_DELIVERY' => 'Y',
]);

/**
 * Оплата
 */
$paymentCollection = $order->getPaymentCollection();

$paySystemService = PaySystem\Manager::getObjectById(8);

if ($amount < $order->getPrice()) {

    $bonusPayment = $paymentCollection->createItem();

    $bonusPayment->setFields([
        'PAY_SYSTEM_ID' => 6,
        'PAY_SYSTEM_NAME' => PaySystem\Manager::getObjectById(6)->getField('NAME'),
        'SUM' => (float)$order->getPrice() - (float)$amount,
    ]);

    $bonusPayment->setField('PAID', 'Y');

    $cardPayment = $paymentCollection->createItem();

    $cardPayment->setFields([
        'PAY_SYSTEM_ID' => $paySystemService->getField('PAY_SYSTEM_ID'),
        'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
        'SUM' => $amount,
        'PAID' => 'Y',
    ]);

} else {

    $payment = $paymentCollection->createItem();

    $payment->setFields([
        'PAY_SYSTEM_ID' => $paySystemService->getField('PAY_SYSTEM_ID'),
        'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
        'SUM' => $order->getPrice(),
        'PAID' => 'Y',
    ]);
}

/**
 * Свойства заказа
 */
$propertyCollection = $order->getPropertyCollection();

if ($prop = $propertyCollection->getItemByOrderPropertyCode('PAYMENT_ID')) {
    $prop->setValue($paymentId);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('EMAIL')) {
    $prop->setValue($fields['email']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('PHONE')) {
    $prop->setValue($fields['phone']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('NAME')) {
    $prop->setValue($fields['name']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('SURNAME')) {
    $prop->setValue($fields['surname']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('CITY')) {
    $prop->setValue($fields['city']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('STREET')) {
    $prop->setValue($fields['street']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('HOUSE')) {
    $prop->setValue($fields['dom']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('APARTMENT')) {
    $prop->setValue($fields['kvartira']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('BONUS')) {
    $prop->setValue($fields['bonusPoints']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('ADDRESS')) {
    $prop->setValue($fields['address_cdek']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('UTM_SOURCE')) {
    $prop->setValue($fields['utmSource']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('UTM_CAMPAIGN')) {
    $prop->setValue($fields['utmCampaign']);
}

if ($prop = $propertyCollection->getItemByOrderPropertyCode('UTM_PARTNER')) {
    $prop->setValue($fields['utmPartner']);
}

/**
 * Сохраняем заказ
 */
$order->doFinalAction(true);

$result = $order->save();

if (!$result->isSuccess()) {

    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/paykeeper_error.log',
        print_r($result->getErrorMessages(), true),
        FILE_APPEND
    );

    http_response_code(500);
    die('ORDER_SAVE_ERROR');
}

$orderId = $order->getId();

/**
 * СДЭК
 */
if ($fields['cdek'] === 'Y') {

    $cdekOrderData = [
        'order_number' => $orderId . '-' . time(),
        'tariff_code' => $fields['tariff_cdek'],
        'recipient_name' => $fields['surname'] . ' ' . $fields['name'],
        'recipient_phone' => $fields['phone'],
        'weight' => 1000,
        'items' => [
            [
                'name' => 'Товар из заказа #' . $orderId,
                'ware_key' => 'BX-' . $orderId,
                'amount' => 1,
                'cost' => $basket->getPrice(),
                'weight' => 1200,
                'payment' => [
                    'value' => 0,
                ],
            ]
        ]
    ];

    if (!empty($fields['pvz_code_cdek'])) {

        $cdekOrderData['pvz_code'] = $fields['pvz_code_cdek'];

    } else {

        $cdekOrderData['to_location'] = [
            'city' => $fields['city_cdek'],
            'address' => $fields['address_cdek'],
            'postal_code' => $fields['postal_code_cdek'],
        ];
    }

    $cdekResult = createCdekOrder($cdekOrderData);

    if (!empty($cdekResult['entity']['uuid'])) {

        $propertyCollection = $order->getPropertyCollection();

        if ($cdekProp = $propertyCollection->getItemByOrderPropertyCode('CDEK_UUID')) {
            $cdekProp->setValue($cdekResult['entity']['uuid']);
            $order->save();
        }
    }
}

/**
 * Помечаем как обработанный
 */
updateHLData(
    'PendingPayments',
    (int)$pendingOrder['ID'],
    [
        'UF_STATUS' => 'PAID',
        'UF_PAYKEEPER_ID' => $paymentId,
    ]
);

echo 'OK';
exit;