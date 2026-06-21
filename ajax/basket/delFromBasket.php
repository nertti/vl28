<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Context;

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

$productId = (int)$_POST['PRODUCT_ID'];

$fUserId = Fuser::getId();
$siteId = Context::getCurrent()->getSite();

$basket = Basket::loadItemsForFUser($fUserId, $siteId);

$deleted = false;

foreach ($basket as $basketItem) {
    if ((int)$basketItem->getProductId() === $productId) {
        $basketItem->delete();
        $deleted = true;
    }
}

if ($deleted) {
    $basket->save();

    $totalQuantity = 0;

    foreach ($basket as $basketItem) {
        $totalQuantity += (int)$basketItem->getQuantity();
    }

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'success',
        'message' => 'Товар удалён из корзины.',
        'count' => $totalQuantity,
        'sum' => $basket->getPrice()
    ]);
} else {

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'error',
        'message' => 'Товар не найден в корзине.'
    ]);
}