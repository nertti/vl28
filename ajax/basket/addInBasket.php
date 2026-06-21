<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Context;

Loader::includeModule("catalog");
Loader::includeModule("sale");

$errors = [];

if (empty($_POST['PRODUCT_ID'])) {
    $errors['PRODUCT_ID'] = 'Нет ID товара.';
}

if (!empty($errors)) {
    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'error',
        'message' => $errors
    ]);

    exit();
}

$fields = [
    'PRODUCT_ID' => (int)$_POST['PRODUCT_ID'],
    'QUANTITY'   => 1,
];

$result = Bitrix\Catalog\Product\Basket::addProduct($fields);

if ($result->isSuccess()) {

    $fUserId = Fuser::getId();
    $siteId = Context::getCurrent()->getSite();

    $basket = Basket::loadItemsForFUser($fUserId, $siteId);

    header('Content-Type: application/json');

    $totalQuantity = 0;

    foreach ($basket as $basketItem) {
        $totalQuantity += (int)$basketItem->getQuantity();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Товар добавлен в корзину.',
        'count' => $totalQuantity,
        'sum' => $basket->getPrice()
    ]);

} else {

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'error',
        'message' => $result->getErrorMessages()
    ]);
}