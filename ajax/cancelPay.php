<?php

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/alfa_auth.php";

use Bitrix\Main\Loader;
use Bitrix\Sale;

header('Content-Type: application/json; charset=UTF-8');

try {

    // Только POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Разрешён только POST-запрос');
    }

    // Подключаем sale
    if (!Loader::includeModule('sale')) {
        throw new Exception('Модуль sale не подключен');
    }

    // Проверяем наличие данных PayKeeper
    if (empty($login) || empty($password)) {
        throw new Exception('Не указаны логин или пароль PayKeeper');
    }

    $orderId = (int)($_POST['ID'] ?? 0);
    $paymentId = trim((string)($_POST['PAYMENT_ID'] ?? ''));

    if ($orderId <= 0) {
        throw new Exception('Некорректный ID заказа');
    }

    // Загружаем заказ
    $order = Sale\Order::load($orderId);

    if (!$order) {
        throw new Exception('Заказ не найден');
    }

    // Получаем свойства заказа
    $propertyCollection = $order->getPropertyCollection();

    // Если PAYMENT_ID не передан — берём из свойства заказа
    if ($paymentId === '') {

        $paymentIdProp = $propertyCollection
            ->getItemByOrderPropertyCode('PAYMENT_ID');

        if ($paymentIdProp) {
            $paymentId = trim((string)$paymentIdProp->getValue());
        }
    }

    if ($paymentId === '') {
        throw new Exception('Не найден ID платежа PayKeeper');
    }

    // ==========================================
    // 1. Проверяем оплату в Битриксе
    // ==========================================

    $paymentCollection = $order->getPaymentCollection();

    $paidPayment = null;

    foreach ($paymentCollection as $payment) {

        if ($payment->isPaid()) {
            $paidPayment = $payment;
            break;
        }
    }

    if (!$paidPayment) {
        throw new Exception('Заказ не имеет оплаченного платежа');
    }

    // ==========================================
    // 2. Возврат в PayKeeper
    // ==========================================

    $payKeeper = new PayKeeper(
        'vl28.server.paykeeper.ru',
        $login,
        $password
    );

    try {

        $payKeeper->reversePayment($paymentId);

    } catch (\Throwable $e) {

        throw new Exception(
            'Ошибка возврата PayKeeper: ' . $e->getMessage()
        );
    }

    // ==========================================
    // 3. Снимаем оплату в Битриксе
    // ==========================================

    foreach ($paymentCollection as $payment) {

        if ($payment->isPaid()) {
            $payment->setPaid('N');
        }
    }

    // ==========================================
    // 4. Отменяем заказ
    // ==========================================

    $order->setField('STATUS_ID', 'C');
    $order->setField('CANCELED', 'Y');
    $order->setField(
        'REASON_CANCELED',
        'Отменён покупателем'
    );

    $result = $order->save();

    if (!$result->isSuccess()) {

        throw new Exception(
            implode(', ', $result->getErrorMessages())
        );
    }

    // ==========================================
    // 5. Бонусы
    // ==========================================

    /*
     * ВАЖНО:
     * Здесь пока ничего не списываем.
     *
     * Нужно отдельно определить:
     * BONUS — это начисленные бонусы
     * или бонусы, которыми пользователь оплатил заказ.
     */

    echo json_encode([
        'status' => 'success',
        'message' => 'Заказ и оплата успешно отменены'
    ], JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}