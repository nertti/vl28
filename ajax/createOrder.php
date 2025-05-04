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

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

// Допустим некоторые поля приходит в запросе
$request = Context::getCurrent()->getRequest();

$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

// Проверяем заполненные значения
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
    if (empty($request['city'])) {
        $errors['city'] = "Пожалуйста, укажите населённый пункт.";
    }
    if (empty($request['street'])) {
        $errors['street'] = "Пожалуйста, укажите улицу.";
    }
    if (empty($request['dom'])) {
        $errors['dom'] = "Пожалуйста, укажите номер дома.";
    }
    if (empty($request['kvartira'])) {
        $errors['kvartira'] = "Пожалуйста, укажите номер квартиры.";
    }
}
// Если есть ошибки, возвращаем их в виде JSON
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}
$phone = $request["phone"];
$phoneCleaned = preg_replace("/[^0-9]/", "", $_POST["phone"]); // Очищаем номер

$name = $request["name"];
$comment = $request["comment"];

$fUserId = $request['fUserId'];
$siteId = $request['siteId'];
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

$userId = $USER->GetID();

if (!$USER->isAuthorized()) {
    $rsUsers = CUser::GetList(array(), 'sort', array('PERSONAL_PHONE' => $phoneCleaned));
    if ($rsUsers->SelectedRowsCount() <= 0) {
        $arResult = $USER->Register($phoneCleaned, "", "", $phoneCleaned, $phoneCleaned, $phoneCleaned . "@vl28.ru");
        if ($arResult['TYPE'] == 'OK') {
            $fields = array(
                "PERSONAL_PHONE" => $phoneCleaned,
            );
            $USER->Update($arResult['ID'], $fields);
            $userId = $USER->GetID();
        }
    } else {
        $rsUser = CUser::GetByLogin($phoneCleaned);
        $arUser = $rsUser->Fetch();
        $userId = $arUser['ID'];
        //$USER->Authorize($arUser['ID']); // авторизуем
    }
    $USER->Logout();
}

// Создаёт новый заказ
$order = Order::create($siteId, $USER->isAuthorized() ? $USER->GetID() : $userId);
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
$paySystemService = PaySystem\Manager::getObjectById(2); //"Наличный расчёт"
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