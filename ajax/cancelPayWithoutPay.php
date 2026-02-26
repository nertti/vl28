<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/t_auth.php';
/** @var $terminalKey */
/** @var $secretKey */

//pr($_POST);
use Bitrix\Sale;

if (CModule::IncludeModule('sale')) {
    $orderId = $_POST['ID'];
    $order = Sale\Order::load($orderId);
    $order->setField('STATUS_ID', 'C'); // статус Отмены
    $order->save();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error']);
}


