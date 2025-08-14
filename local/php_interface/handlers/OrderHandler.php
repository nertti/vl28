<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;

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

    if (!$isNew) {
        return; // только при создании
    }

    Loader::includeModule("sale");

    $orderId = $order->getId();
    $price = $order->getPrice();
    $currency = $order->getCurrency();
    $userId = $order->getUserId();

    // Данные пользователя
    $user = \Bitrix\Main\UserTable::getById($userId)->fetch();
    $userName = trim($user["NAME"] . " " . $user["LAST_NAME"]);
    $userEmail = $user["EMAIL"];

    // Список товаров
    $basket = $order->getBasket();
    $items = [];
    foreach ($basket as $basketItem) {
        $items[] = $basketItem->getField("NAME") . " x" . $basketItem->getQuantity();
    }
    $itemsList = implode("\n", $items);

    // Сообщение
    $message = "🆕 Новый заказ #$orderId\n"
        . "👤 Клиент: {$userName}\n"
        . "📧 Email: {$userEmail}\n"
        . "💰 Сумма: {$price} {$currency}\n"
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