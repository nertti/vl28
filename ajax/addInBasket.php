<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Loader::includeModule("catalog");
$errors = [];
$arFields = [];
if (empty($_POST['PRODUCT_ID'])) {
    $errors['PRODUCT_ID'] = "Нет ID товара.";
}
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

$fields = [
    'PRODUCT_ID' => $_POST['PRODUCT_ID'], // ID товара
    'QUANTITY' => 1, // количество, обязательно
];
$r = Bitrix\Catalog\Product\Basket::addProduct($fields);
if ($r->isSuccess()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Товар добавлен в корзину.']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $r->getErrorMessages()]);
}