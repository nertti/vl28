<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\DiscountCouponsManager;

Loader::includeModule('sale');
Loader::includeModule('catalog');
Loader::includeModule('highloadblock');

require_once $_SERVER['DOCUMENT_ROOT'] . '/ajax/cdek/create_cdek_order.php';

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$postData = $request->getPostList()->toArray();
$getData = $request->getQueryList()->toArray();

// Визит браузера (редирект PayKeeper) — не обрабатываем как callback
if (!$request->isPost() || empty($postData['orderid'])) {
    $tmpOrderId = $getData['tmp_order_id'] ?? $getData['orderid'] ?? '';
    $redirectUrl = '/ajax/paySuccess.php';

    if (($getData['result'] ?? '') === 'fail') {
        LocalRedirect('/ajax/payError.php');
    }

    if ($tmpOrderId !== '') {
        $redirectUrl .= '?tmp_order_id=' . urlencode($tmpOrderId);
    } elseif (!empty($getData['payment_id'])) {
        $redirectUrl .= '?payment_id=' . urlencode($getData['payment_id']);
    }

    LocalRedirect($redirectUrl);
}

$orderTempId = $postData['orderid'] ?? '';
$paymentId = $postData['id'] ?? '';
$amount = (float)($postData['sum'] ?? 0);

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/orderFieldsLog.txt', print_r($postData, 1), FILE_APPEND);

if (empty($orderTempId)) {
    http_response_code(400);
    die('ORDER_ID_EMPTY');
}

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

if ($pendingOrder['UF_STATUS'] === 'PAID') {
    echo 'OK ' . ($postData['id'] ?? '');
    exit;
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
$bonusWithdraw = (float)($fields['bonusWithdraw'] ?? 0);
$expectedAmount = (float)($fields['payAmount'] ?? 0);

$basket = Basket::loadItemsForFUser($fUserId, $siteId);

if ($basket->isEmpty()) {
    http_response_code(500);
    die('BASKET_EMPTY');
}

DiscountCouponsManager::clear(true);
if (!empty($fields['promocode'])) {
    DiscountCouponsManager::add($fields['promocode']);
}
$basket->refreshData(['PRICE', 'COUPON']);

$order = Order::create($siteId, $userId);
$order->setPersonTypeId(1);
$order->setBasket($basket);
$order->setField('USER_DESCRIPTION', $fields['comment'] ?? '');

$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$shipmentItemCollection = $shipment->getShipmentItemCollection();

foreach ($basket as $basketItem) {
    $shipmentItem = $shipmentItemCollection->createItem($basketItem);
    $shipmentItem->setQuantity($basketItem->getQuantity());
}

$deliveryId = (int)($fields['delivery'] ?? 1);
$service = Delivery\Services\Manager::getById($deliveryId);
$deliveryService = Delivery\Services\Manager::getObjectById($deliveryId);

if ($deliveryService) {
    $shipment->setDeliveryService($deliveryService);
}

$shipment->setFields([
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
    'BASE_PRICE_DELIVERY' => $fields['deliveryPrice'],
    'PRICE_DELIVERY' => $fields['deliveryPrice'],
    'CUSTOM_PRICE_DELIVERY' => 'Y',
]);

$order->doFinalAction(true);

$orderPrice = (float)$order->getPrice();
$cardAmount = $amount > 0 ? $amount : max(0, $orderPrice - $bonusWithdraw);

if ($expectedAmount > 0 && abs($cardAmount - $expectedAmount) > 0.02) {
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/local/payInfoLog.txt',
        date('Y-m-d H:i:s') . " AMOUNT_MISMATCH tmp={$orderTempId} paid={$cardAmount} expected={$expectedAmount} order={$orderPrice}\n",
        FILE_APPEND
    );
}

$paymentCollection = $order->getPaymentCollection();
$paySystemService = PaySystem\Manager::getObjectById(8);

if ($bonusWithdraw > 0) {
    $bonusPayment = $paymentCollection->createItem();
    $bonusPayment->setFields([
        'PAY_SYSTEM_ID' => 6,
        'PAY_SYSTEM_NAME' => PaySystem\Manager::getObjectById(6)->getField('NAME'),
        'SUM' => $bonusWithdraw,
    ]);
    $bonusPayment->setField('PAID', 'Y');
}

$cardPayment = $paymentCollection->createItem();
$cardPayment->setFields([
    'PAY_SYSTEM_ID' => $paySystemService->getField('PAY_SYSTEM_ID'),
    'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
    'SUM' => $cardAmount,
    'PAID' => 'Y',
]);

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

$order->doFinalAction(true);

$orderId = $order->getId();

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
    $cdekResultInfo = getCdekInfo($cdekResult['entity']['uuid']);


    if (!empty($cdekResult['entity']['uuid'])) {
        $propertyCollection = $order->getPropertyCollection();

        if ($cdekProp = $propertyCollection->getItemByOrderPropertyCode('CDEK_UUID')) {
            $cdekProp->setValue($cdekResult['entity']['uuid']);
        }
        if ($cdekNumberProp = $propertyCollection->getItemByOrderPropertyCode('CDEK_NUMBER')) {
            $cdekNumberProp->setValue($cdekResultInfo['entity']['number']);
        }
    }
}

$fields['bitrixOrderId'] = $orderId;
$order->save();

updateHLData(
    'PendingPayments',
    (int)$pendingOrder['ID'],
    [
        'UF_STATUS' => 'PAID',
        'UF_PAYKEEPER_ID' => $paymentId,
        'UF_DATA' => json_encode($fields, JSON_UNESCAPED_UNICODE),
    ]
);

echo 'OK ' . ($postData['id'] ?? '');
exit;
