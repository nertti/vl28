<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\PaySystem;

Loader::includeModule('sale');
Loader::includeModule('catalog');

session_start();
$orderId = $_GET['OrderId'];
$paymentId = $_GET['PaymentId'];
$amount = $_GET['Amount'];
$success = $_GET['Success'] === 'true' || $_GET['Success'] === '1';
$phoneCleaned = $_SESSION['PENDING_ORDER'][$orderId]['FIELDS']['phone'];

require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/t_auth.php';

if ($success) {
    // === Заказа нет — создаём новый ===
    if (!isset($_SESSION['PENDING_ORDER'][$orderId])) {
        echo "<h2 style='text-align:center;color:red'>Ошибка: данные заказа не найдены.</h2>";
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
        exit;
    }

    $fields = $_SESSION['PENDING_ORDER'][$orderId]['FIELDS'];
    $siteId = $fields['siteId'];
    $fUserId = $fields['fUserId'];

    // === Загружаем корзину покупателя ===
    $basket = Basket::loadItemsForFUser($fUserId, $siteId);
    if (!$USER->isAuthorized()) {
        $userId = 44;
    }

    $order = Order::create($siteId, $USER->isAuthorized() ? $USER->GetID() : $userId);
    // === Создаём заказ ===
    $order->setField('USER_DESCRIPTION', $fields['comment']);
    $order->setBasket($basket);

    // === Добавляем доставку ===
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $service = Delivery\Services\Manager::getById($fields['delivery'] ?? 1);
    $shipment->setFields([
        'DELIVERY_ID' => $service['ID'],
        'DELIVERY_NAME' => $service['NAME'],
    ]);

    // === Добавляем оплату (Тиньков) ===
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection->createItem();
    $paySystemService = PaySystem\Manager::getObjectById(7); // ID твоей платёжной системы "Тиньков"
    $payment->setFields([
        'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
        'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
        'SUM' => $order->getPrice(),
        'PAID' => 'Y',
    ]);

    // === Заполняем свойства ===
    $propertyCollection = $order->getPropertyCollection();
    $propertyCollection->getItemByOrderPropertyCode('EMAIL')->setValue($fields['email']);
    $propertyCollection->getItemByOrderPropertyCode('PHONE')->setValue($fields['phone']);
    $propertyCollection->getItemByOrderPropertyCode('NAME')->setValue($fields['name']);
    $propertyCollection->getItemByOrderPropertyCode('SURNAME')->setValue($fields['surname']);

    // === Сохраняем заказ ===
    $order->doFinalAction(true);
    $order->save();

    unset($_SESSION['PENDING_ORDER'][$orderId]); // очищаем временные данные
}

// === Если заказ найден и Success ===
if ($order && $success) {
    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
        $payment->setPaid('Y');
    }

    $order->setField('STATUS_ID', 'P'); // например, статус "Оплачен"
    $order->save();

    // бонусы, если нужно
    $propertyCollection = $order->getPropertyCollection();
    $bonusProp = $propertyCollection->getItemByOrderPropertyCode('BONUS');
    if ($bonusProp) $bonusProp->setValue(0);
    $bonusCreditedProp = $propertyCollection->getItemByOrderPropertyCode('BONUS_CREDITED');
    if ($bonusCreditedProp) $bonusCreditedProp->setValue('Y');

    $order->save();
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background-color: #f0f4f8;
    }

    .success-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 90%;
    }

    .success-title {
        color: #000000;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .success-message {
        color: #34495E;
        font-size: 18px;
        line-height: 1.5;
        margin-bottom: 25px;
    }

    .order-details {
        background: #F8F9FA;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .details-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
        border-bottom: 1px solid #E9ECEF;
    }

    .details-label {
        color: #666;
        font-weight: 500;
    }

    .details-value {
        color: #222;
        font-weight: 600;
    }

    .continue-button {
        background-color: #000000;
        color: white;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .continue-button:hover {
        background-color: #000000;
    }
</style>

<div class="success-container">
    <h1 class="success-title">Спасибо за ваш заказ!</h1>
    <p class="success-message">
        Ваш платеж был успешно оплачен.<br>
        Мы отправили детали вашего заказа на ваш email.
    </p>

    <?php if ($order): ?>
        <div class="order-details">
            <div class="details-item">
                <span class="details-label">Номер заказа:</span>
                <span class="details-value">#<?= $order->getId() ?></span>
            </div>
            <div class="details-item">
                <span class="details-label">Сумма:</span>
                <span class="details-value"><?= number_format($amount / 100, 2, ',', ' ') ?> ₽</span>
            </div>
        </div>
    <?php endif; ?>

    <button class="continue-button" onclick="window.location.href='/catalog/'">
        Вернуться в магазин
    </button>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
