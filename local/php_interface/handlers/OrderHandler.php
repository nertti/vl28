<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;

function onOrderPaid($order_id, &$arFields)
{
    if ($arFields['PAYED'] == 'Y' && $arFields['ORDER_PROP'][23] != 'Y') {

        $bonus = (int)$arFields['ORDER_PROP'][21]; // сумма бонусов
        $userId = (int)$arFields['USER_ID'];

        // Проверяем внутренний счёт пользователя
        $userBalance = CSaleUserAccount::GetByUserID($userId, "RUB");

        if (!$userBalance) {
            $arFieldsAcc = [
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => $bonus
            ];
            $accountID = CSaleUserAccount::Add($arFieldsAcc);
        } else {
            $arFieldsAcc = [
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => (float)$userBalance['CURRENT_BUDGET'] + (float)$bonus,
            ];
            $accountID = CSaleUserAccount::Update($userBalance['ID'], $arFieldsAcc);
        }

        // Проставляем отметку "начислено"
        $db_props = CSaleOrderPropsValue::GetList([], ["ORDER_ID" => $order_id]);
        while ($prop = $db_props->Fetch()) {
            // Ищем свойство с ID = 23
            if ($prop["ORDER_PROPS_ID"] == 23) {
                CSaleOrderPropsValue::Update($prop["ID"], ["VALUE" => "Y"]);
                break;
            }
        }

        // Добавляем запись в историю заказа (опционально)
        CSaleOrderChange::AddRecord(
            $order_id,
            "COMMENT",
            ["COMMENT" => "Начислены бонусы пользователю ID {$userId}: +{$bonus} руб."]
        );
    }
}

function onOrderCreate(Bitrix\Main\Event $event)
{

    //return;
//    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($sendTelegram, 1), FILE_APPEND);
//    $telegramToken = "8332872680:AAG1OtqE-zZKpCXghJFjPQAzKuFWvMzlV4U";
//    $chatId = "-1002635999993";

    \Bitrix\Main\Loader::includeModule('highloadblock');

// ID хайлоад-блока
    $hlblockId = 3;

// Получаем данные хайлоада
    $hlblock = HighloadBlockTable::getById($hlblockId)->fetch();
    $entity = HighloadBlockTable::compileEntity($hlblock);
    $dataClass = $entity->getDataClass();

// Получаем первую запись
    $result = $dataClass::getList([
        'select' => ['*'],
        'order'  => ['ID' => 'ASC'],
        'limit'  => 1
    ]);

    $firstItem = $result->fetch();

// Подставляем значения
    $telegramToken = $firstItem['UF_BOT_TOKEN'];
    $chatId        = $firstItem['UF_ID_CHAT'];

    $adminOrderUrl = "https://vl28.pro/bitrix/admin/sale_order_view.php?ID=";

    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    \Bitrix\Main\Loader::includeModule("sale");

    $orderId = $order->getId();
    $propertyCollection = $order->getPropertyCollection();


    // Проверяем свойство SEND_TELEGRAM
    $sendTelegramProp = $propertyCollection->getItemByOrderPropertyId(29); // ID свойства SEND_TELEGRAM
    if ($sendTelegramProp && $sendTelegramProp->getValue() === 'Y') {
        return; // Уже отправляли
    }

    // Доставка
    $deliveryIds = $order->getDeliverySystemId();
    $deliveryId = is_array($deliveryIds) && count($deliveryIds) > 0 ? $deliveryIds[0] : null;
    $service = null;
    if ($deliveryId) {
        $service = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryId);
    }

    // Способ оплаты
    $paymentCollection = $order->getPaymentCollection();
// Основное условие отправки
    $sendTelegram = false;
    foreach ($paymentCollection as $paymentItem) {
        $paySystemId = $paymentItem->getPaymentSystemId();
        if ($paySystemId == 7) {
            $sendTelegram = true;
            break; // Достаточно одной оплаты с ID = 7
        }
    }


// Если нет платежа с ID = 7 и заказ не оплачен другими способами, тоже отправляем
    if (!$sendTelegram) {
        foreach ($paymentCollection as $paymentItem) {
            $paySystemId = $paymentItem->getPaymentSystemId();
            if ($paySystemId != 7 && !$paymentItem->isPaid()) {
                $sendTelegram = true;
                break;
            }
        }
    }

    if (!$sendTelegram) {
        return; // Не отправлять
    }

    // Данные пользователя
    $userName = $propertyCollection->getItemByOrderPropertyId(13)->getValue() . " " . $propertyCollection->getItemByOrderPropertyId(14)->getValue();
    $userEmail = $propertyCollection->getItemByOrderPropertyId(12)->getValue();
    $userPhone = $propertyCollection->getItemByOrderPropertyId(15)->getValue();

    // Список товаров
    $basket = $order->getBasket();
    $items = [];
    foreach ($basket->getListOfFormatText() as $basketItem) {
        $items[] = html_entity_decode($basketItem);
    }
    $itemsList = implode("\n", $items);

    // Адрес доставки
    $city = $propertyCollection->getItemByOrderPropertyId(17)->getValue();
    $street = $propertyCollection->getItemByOrderPropertyId(18)->getValue();
    $home = $propertyCollection->getItemByOrderPropertyId(19)->getValue();
    $apartment = $propertyCollection->getItemByOrderPropertyId(20)->getValue();
    $address_cdek = $propertyCollection->getItemByOrderPropertyId(31)->getValue();
    $parts = array_filter([$city, $street, $home, $apartment]);
    $address = implode(', ', $parts);

    $cdek = $propertyCollection->getItemByOrderPropertyId(30)->getValue();

    // Проверяем оплату бонусами (ID = 6) и суммируем
    $bonusPaidAmount = 0;
    foreach ($paymentCollection as $paymentItem) {
        if ($paymentItem->getPaymentSystemId() == 6 && $paymentItem->isPaid()) {
            $bonusPaidAmount += $paymentItem->getSum();
        }
    }
    $amount = $order->getPrice() - $bonusPaidAmount;
    // Статус оплаты
    $payStatus = $order->isPaid() ? "✅ Заказ оплачен" : "❌ Заказ не оплачен";
    $payMethod = $order->isPaid() ? "Оплата онлайн" : "Оплата при получении";

    // Скидки
    $discountsText = "";
    $discounts = $order->getDiscount()->getApplyResult(false);
    if (!empty($discounts["DISCOUNT_LIST"])) {
        $discountsList = array_shift($discounts['PRICES']['BASKET']);
        $currency = $order->getCurrency();
        $discountsText .= "💸 Итоговая скидка: {$discountsList['DISCOUNT']} {$currency}\n";
    } else {
        $discountsText = "Нет применённых скидок\n";
    }

    // Сообщение
    $message = ($isNew ? "🆕 Новый заказ #$orderId\n" : "💳 Оплата заказа #$orderId\n")
        . "{$payStatus}\n\n"
        . "🚚 Доставка: " . ($service ? $service['NAME'] : "Неизвестно") . "\n"
        . ($cdek ? "🚚 CDEK_UUID: " . $cdek : "") . "\n"
        . "🏠 Адрес доставки: {($address ? $address : $address_cdek)}\n\n"
        . "👤 Клиент: {$userName}\n"
        . "📧 Email: {$userEmail}\n"
        . "📞 Телефон: +{$userPhone}\n\n"
        . "💰 Сумма: {$amount} {$order->getCurrency()}\n"
        . "{$discountsText}\n"
        . "💰 Способ оплаты: {$payMethod}\n";

// Если есть оплата бонусами, добавляем отдельную строку
    if ($bonusPaidAmount > 0) {
        $message .= "🎁 Оплачено бонусами: {$bonusPaidAmount} {$order->getCurrency()}\n";
    }

    $message .= "📦 Товары:\n{$itemsList}";

    // Кнопка для открытия заказа
    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "Открыть заказ в админке", "url" => $adminOrderUrl . $orderId]
            ]
        ]
    ];

// Отправка в Telegram с дебагом
    $url = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
    $postFields = [
        "chat_id" => $chatId,
        "text" => $message,
        "parse_mode" => "HTML",
        "reply_markup" => json_encode($keyboard, JSON_UNESCAPED_UNICODE)
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $postFields,
    ]);

    $response = curl_exec($ch);
    $curlErrNo = curl_errno($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

// Логирование
    $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/log.txt';

    if ($curlErrNo) {
        file_put_contents($logPath, "\n[CURL ERROR] №{$curlErrNo}: {$curlError}\n", FILE_APPEND);
    } else {
        //file_put_contents($logPath, "\n[TELEGRAM RESPONSE] HTTP {$httpCode}: {$response}\n", FILE_APPEND);
    }

// Проверяем, что Telegram вернул ok = true
    $ok = false;
    if ($response) {
        $decoded = json_decode($response, true);
        if (!empty($decoded['ok'])) {
            $ok = true;
        } else {
            file_put_contents(
                $logPath,
                "\n[TELEGRAM ERROR] Telegram вернул ошибку: " . print_r($decoded, true) . "\n",
                FILE_APPEND
            );
        }
    }

// Если успех — записываем SEND_TELEGRAM = Y
    if ($ok) {
        $sendTelegramProp->setValue('Y');
        $order->save();
        //file_put_contents($logPath, "\n[OK] Флаг SEND_TELEGRAM установлен для заказа {$orderId}\n", FILE_APPEND);
    } else {
        file_put_contents($logPath, "\n[FAIL] Сообщение не отправлено в Telegram для заказа {$orderId}\n", FILE_APPEND);
    }

}

function onOrderPaidHandler($order_id, &$arFields)
{
    if ($arFields['PAYED'] == 'Y') {
        $userId = $arFields['USER_ID'];
        recalculateUserSummaryPay($userId);
    }
}