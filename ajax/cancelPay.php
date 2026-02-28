<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/t_auth.php';

use Bitrix\Main\Loader;
use Bitrix\Sale;

header('Content-Type: application/json');

if (!Loader::includeModule('sale')) {
    echo json_encode(['status' => 'error', 'message' => 'Модуль sale не подключен']);
    exit;
}

$orderId   = (int)$_POST['ID'];
$paymentId = $_POST['PAYMENT_ID'];

$order = Sale\Order::load($orderId);

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Заказ не найден']);
    exit;
}

try {

    // =============================
    // 1️⃣ Снимаем оплату в Битриксе
    // =============================

    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment) {
        if ($payment->getField('PAID') === 'Y') {
            $payment->setField('PAID', 'N');
        }
    }

    // =============================
    // 2️⃣ Ставим статус отмены
    // =============================

    $order->setField('STATUS_ID', 'C');

    $result = $order->save();

    if (!$result->isSuccess()) {
        throw new Exception(implode(', ', $result->getErrorMessages()));
    }

    // =============================
    // 3️⃣ Отправляем Cancel в Т-Банк
    // =============================

    $data = [
        "TerminalKey" => $terminalKey,
        "PaymentId"   => $paymentId,
        "Password"    => $secretKey
    ];

    ksort($data);
    $token = hash('sha256', implode('', $data));

    $requestData = [
        "TerminalKey" => $terminalKey,
        "PaymentId"   => $paymentId,
        "Token"       => $token,
    ];

    $ch = curl_init('https://securepay.tinkoff.ru/v2/Cancel');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Ошибка запроса к Т-Банку');
    }

    $responseData = json_decode($response, true);

    if (!$responseData['Success']) {
        throw new Exception($responseData['Message']);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Заказ и оплата успешно отменены'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}