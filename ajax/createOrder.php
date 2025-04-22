<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");



use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;

global $USER;

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

// Допустим некоторые поля приходит в запросе
$request = Context::getCurrent()->getRequest();

$phone = $request["phone"];
$name = $request["name"];
$comment = $request["comment"];

$fUserId = $request['fUserId'];
$siteId = $request['siteId'];
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

// Создаёт новый заказ
$order = Order::create($siteId, $USER->isAuthorized() ? $fUserId : 539);
$order->setPersonTypeId(1);
if ($comment) {
    $order->setField('USER_DESCRIPTION', $comment); // Устанавливаем поля комментария покупателя
}
$order->setBasket($basket);

// Создаём одну отгрузку и устанавливаем способ доставки - "Без доставки" (он служебный)
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
$shipment->setFields(array(
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
));
$shipmentItemCollection = $shipment->getShipmentItemCollection();

// Создаём оплату со способом #1
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem();
$paySystemService = PaySystem\Manager::getObjectById(1);
$payment->setFields(array(
    'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
    'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
));

// Устанавливаем свойства
$propertyCollection = $order->getPropertyCollection();
//$phoneProp = $propertyCollection->getPhone();
//$phoneProp->setValue($phone);
//$nameProp = $propertyCollection->getPayerName();
//$nameProp->setValue($name);

// Сохраняем
$order->doFinalAction(true);
$result = $order->save();
$orderId = $order->getId();

if ($result) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Заказ успешно добавлен.', 'data' => $result]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $result]);
}