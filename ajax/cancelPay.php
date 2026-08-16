<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/alfa_auth.php';

use Bitrix\Main\Loader;
use Bitrix\Sale;

/** @var string $login */
/** @var string $password */

header('Content-Type: application/json');

if (!Loader::includeModule('sale')) {
    echo json_encode(['status' => 'error', 'message' => 'Модуль sale не подключен']);
    exit;
}

$orderId = (int)($_POST['ID'] ?? 0);
$paymentId = trim((string)($_POST['PAYMENT_ID'] ?? ''));

$order = Sale\Order::load($orderId);

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Заказ не найден']);
    exit;
}

try {
    $propertyCollection = $order->getPropertyCollection();

    if ($paymentId === '') {
        $paymentIdProp = $propertyCollection->getItemByOrderPropertyCode('PAYMENT_ID');
        if ($paymentIdProp) {
            $paymentId = trim((string)$paymentIdProp->getValue());
        }
    }

    if ($paymentId === '') {
        throw new Exception('Не найден ID платежа PayKeeper');
    }

    // =============================
    // 1️⃣ Возврат в PayKeeper
    // =============================

    $payKeeper = new PayKeeper(
        'vl28.server.paykeeper.ru',
        $login,
        $password
    );

    $payKeeper->reversePayment($paymentId);

    // =============================
    // 2️⃣ Снимаем оплату в Битриксе
    // =============================

    $paymentCollection = $order->getPaymentCollection();

    foreach ($paymentCollection as $payment) {
        if ($payment->getField('PAID') === 'Y') {
            $payment->setField('PAID', 'N');
        }
    }

    // =============================
    // 3️⃣ Ставим статус отмены
    // =============================

    $order->setField('STATUS_ID', 'C');
    $order->setField('CANCELED', 'Y');
    $order->setField('REASON_CANCELED', 'Отменён покупателем');

    $result = $order->save();

    if (!$result->isSuccess()) {
        throw new Exception(implode(', ', $result->getErrorMessages()));
    }

    // =============================
    // 4️⃣ Списание начисленных бонусов
    // =============================

    $bonusProp = $propertyCollection->getItemByOrderPropertyCode('BONUS');
    $bonus = $bonusProp ? (float)$bonusProp->getValue() : 0;

    if ($bonus > 0) {
        CSaleUserAccount::Withdraw($order->getUserId(), $bonus, 'RUB');
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
