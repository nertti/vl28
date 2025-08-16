<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;


function onOrderPaid($order_id, &$arFields)
{
    if ($arFields['PAYED'] == 'Y' && $arFields['ORDER_PROP'][23] != 'Y') {

        $bonus = $arFields['ORDER_PROP'][21];
        $userId = $arFields['USER_ID'];

        // Проверяем существование внутреннего счета пользователя
        $userBalance = CSaleUserAccount::GetByUserID($userId, "RUB");

        if (!$userBalance) {
            $arFieldsAcc = array(
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => $bonus
            );
            $accountID = CSaleUserAccount::Add($arFieldsAcc);
        } else {
            $arFieldsAcc = array(
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => $userBalance['CURRENT_BUDGET'] + $bonus,
            );
            $accountID = CSaleUserAccount::Update($userBalance['ID'], $arFieldsAcc);
        }
    }
}

function onOrderCreate(Bitrix\Main\Event $event)
{
    $telegramToken = "8332872680:AAG1OtqE-zZKpCXghJFjPQAzKuFWvMzlV4U";
    $chatId = "-1002635999993";
    $adminOrderUrl = "http://vl26908655.nichost.ru/bitrix/admin/sale_order_view.php?ID=";

    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    \Bitrix\Main\Loader::includeModule("sale");

    // Условие: новый заказ или обновление, но заказ оплачен
    if (!$isNew && !$order->isPaid()) {
        return;
    }

    Loader::includeModule("sale");

    $orderId   = $order->getId();
    $price     = $order->getPrice();
    $discount  = $order->getDiscountPrice();
    $currency  = $order->getCurrency();
    $userId    = $order->getUserId();
    $propertyCollection = $order->getPropertyCollection();

    // Данные пользователя
    $user = \Bitrix\Main\UserTable::getById($userId)->fetch();
    $userName  = trim($user["NAME"] . " " . $user["LAST_NAME"]);
    $userEmail = $user["EMAIL"];
    $userPhone = $user["PERSONAL_PHONE"];

    // Список товаров
    $basket = $order->getBasket();
    $items = [];
    foreach ($basket->getListOfFormatText() as $basketItem) {
        $items[] = html_entity_decode($basketItem);
    }
    $itemsList = implode("\n", $items);

    // Доставка
    $deliveryIds = $order->getDeliverySystemId();
    $service = null;
    if (is_array($deliveryIds) && count($deliveryIds) > 0) {
        $service = \Bitrix\Sale\Delivery\Services\Manager::getById($deliveryIds[0]);
    }

    // Адрес доставки
    $city = $propertyCollection->getItemByOrderPropertyId(17)->getValue();
    $street = $propertyCollection->getItemByOrderPropertyId(18)->getValue();
    $home = $propertyCollection->getItemByOrderPropertyId(19)->getValue();
    $apartment = $propertyCollection->getItemByOrderPropertyId(20)->getValue();
    $address = $city . ', ' . $street . ', ' . $home . ', ' . $apartment;

    // Статус оплаты
    $payStatus = $order->isPaid() ? "✅ Заказ оплачен" : "❌ Заказ не оплачен";

    // --- СКИДКИ ---
    $discountsText = "";
    $discounts = $order->getDiscount()->getApplyResult(false);

    if (!empty($discounts["DISCOUNT_LIST"])) {
        $discountsList = array_shift($discounts['PRICES']['BASKET']);
        $discountsText .= "💸 Итоговая скидка: {$discountsList['DISCOUNT']} {$currency}\n";
    } else {
        $discountsText = "Нет применённых скидок\n";
    }

    // Сообщение
    $message = ($isNew ? "🆕 Новый заказ #$orderId\n" : "💳 Оплата заказа #$orderId\n")
        . "{$payStatus}\n\n"
        . "🚚 Доставка: " . ($service ? $service['NAME'] : "Неизвестно") . "\n"
        . "🏠 Адрес доставки: {$address}\n\n"
        . "👤 Клиент: {$userName}\n"
        . "📧 Email: {$userEmail}\n"
        . "📞 Телефон: {$userPhone}\n\n"
        . "💰 Сумма: {$price} {$currency}\n"
        . "{$discountsText}\n"
        . "📦 Товары:\n{$itemsList}";

    // Кнопка для открытия заказа
    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "Открыть заказ в админке", "url" => $adminOrderUrl . $orderId]
            ]
        ]
    ];

    // Отправка в Telegram
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
    curl_close($ch);
}
