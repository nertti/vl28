<?php
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Sale;

if (CModule::IncludeModule('sale')) {

// нужно перевести статус в "оплачено"
// проставить оплату заказа

    if ($_GET['Success']) {
        $orderId = $_GET['OrderId'];

        $order = Sale\Order::load($orderId);
        $paymentCollection = $order->getPaymentCollection();
        foreach ($paymentCollection as $payment) {
            $payment->setPaid('Y');
        }
        $order->setField('STATUS_ID', 'P'); // статус
        $order->save();

        $propertyCollection = $order->getPropertyCollection();
        $bonusProp = $propertyCollection->getItemByOrderPropertyCode('BONUS');
        $bonusProp->setValue(0);
        $bonusCreditedProp = $propertyCollection->getItemByOrderPropertyCode('BONUS_CREDITED');
        $bonusCreditedProp->setValue('Y');

        $order->save();

    }
}