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
}
$data = [
    "TerminalKey" => $_POST['TERMINAL_KEY'],
    "PaymentId" => $_POST['PAYMENT_ID'],
    'Password' => $secretKey

];


ksort($data);
$token = hash('sha256', implode('', $data));

// Тело запроса
$requestData = [
    "TerminalKey" => $_POST['TERMINAL_KEY'],
    "PaymentId" => $_POST['PAYMENT_ID'],
    "Token" => $token,
];
// Инициализация сессии cURL
$ch = curl_init('https://securepay.tinkoff.ru/v2/Cancel');

try {
    // Настраиваем заголовки для JSON
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    // Кодирование данных в JSON
    $jsonData = json_encode($requestData);

    // Настраиваем параметры cURL
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 30
    ]);

    // Выполняем запрос
    $response = curl_exec($ch);

    // Получаем информацию о запросе
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Проверяем успешность запроса
    if ($httpCode === 200) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $response]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'data' => $e->getMessage()]);
} finally {
    curl_close($ch);
}