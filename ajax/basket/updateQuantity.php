<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale;

header('Content-Type: application/json');

if (!\Bitrix\Main\Loader::includeModule('sale')) {
    echo json_encode([
        'success' => false,
        'message' => 'Module sale error'
    ]);
    exit;
}


$basketId = (int)$_POST['BASKET_ID'];
$quantity = (int)$_POST['QUANTITY'];


if (!$basketId || $quantity < 1) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid data'
    ]);

    exit;
}


$fUserId = Sale\Fuser::getId();

$siteId = \Bitrix\Main\Context::getCurrent()->getSite();


$basket = Sale\Basket::loadItemsForFUser(
    $fUserId,
    $siteId
);


$basketItem = $basket->getItemById($basketId);


if (!$basketItem) {

    echo json_encode([
        'success' => false,
        'message' => 'Basket item not found'
    ]);

    exit;
}


// меняем количество
$basketItem->setField(
    'QUANTITY',
    $quantity
);


// сохраняем
$result = $basket->save();


if (!$result->isSuccess()) {

    echo json_encode([
        'success' => false,
        'errors' => $result->getErrorMessages()
    ]);

    exit;

}


// пересчёт скидок

$order = Sale\Order::create(
    $siteId,
    $fUserId
);

$order->setPersonTypeId(1);
$order->setBasket($basket);

$order->doFinalAction(true);


$basketItem = $basket->getItemById($basketId);


$total = 0;

foreach ($basket as $item) {
    $total += $item->getFinalPrice();
}


$basePrice =
    $basketItem->getBasePrice()
    * $basketItem->getQuantity();


$finalPrice =
    $basketItem->getFinalPrice();


$hasDiscount =
    $basePrice > $finalPrice;


echo json_encode([

    'success' => true,

    'quantity' => $basketItem->getQuantity(),

    'itemPrice' => $finalPrice,

    'itemBasePrice' => $basePrice,

    'hasDiscount' => $hasDiscount,

    'totalPrice' => $total

]);