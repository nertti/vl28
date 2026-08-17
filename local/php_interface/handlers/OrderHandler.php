<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Helpers\Order as OrderHelper;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Main\Event;
use Bitrix\Main\Mail\Event as MailEvent;
use Bitrix\Highloadblock as HL;
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
    //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($firstItem, 1), FILE_APPEND);

// Подставляем значения
    $telegramToken = $firstItem['UF_BOT_TOKEN'];
    $chatId        = $firstItem['UF_ID_CHAT'];
//    $telegramToken = "8332872680:AAG1OtqE-zZKpCXghJFjPQAzKuFWvMzlV4U";
//    $chatId = "-1002635999993";
    $adminOrderUrl = "https://vl28.pro/bitrix/admin/sale_order_view.php?ID=";

    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    $userId = $order->getUserId();

    if ($userId) {
        $user = new CUser();
        $userGroups = CUser::GetUserGroup($userId);

        if (in_array(8, $userGroups)) {
            // Удаляем группу 8
            $newGroups = array_diff($userGroups, [8]);

            // Обновляем группы пользователя
            $user->SetUserGroup($userId, $newGroups);
        }
    }

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
    $itemsList = implode("\n", preg_replace('/\[[^\]]*\]/u', '', $items));

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

    // ================= ПРОМОКОД =================

// получаем применённые купоны
    $coupons = DiscountCouponsManager::get(true);

    $promoCode = '';
    $promoDiscount = 0;

    foreach ($coupons as $coupon) {
        if ($coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
            $promoCode = $coupon['COUPON'];
            break;
        }
    }

// получаем скидки
    $discountData = $order->getDiscount()->getApplyResult(true);

    if (!empty($discountData['PRICES']['BASKET'])) {
        foreach ($discountData['PRICES']['BASKET'] as $item) {
            if (!empty($item['DISCOUNT'])) {
                $promoDiscount += $item['DISCOUNT'];
            }
        }
    }

    $promoDiscount = round($promoDiscount);

//    // Скидки
//    $discountsText = "";
//    $discounts = $order->getDiscount()->getApplyResult(false);
//    if (!empty($discounts["DISCOUNT_LIST"])) {
//        $discountsList = array_shift($discounts['PRICES']['BASKET']);
//        $currency = $order->getCurrency();
//        $discountsText .= "💸 Итоговая скидка: {$discountsList['DISCOUNT']} {$currency}\n";
//    } else {
//        $discountsText = "Нет применённых скидок\n";
//    }

    $currency = $order->getCurrency();

    if ($promoCode) {
        $discountsText = "🎟 Промокод: {$promoCode}\n";
        $discountsText .= "💸 Скидка: {$promoDiscount} {$currency}\n";
    } else {
        $discountsText = "💸 Промокод не применён\n";
    }

    $deliveryAddress = $address ?: $address_cdek;
    // Сообщение
    $message = ($isNew ? "🆕 Новый заказ #$orderId\n" : "💳 Оплата заказа #$orderId\n")
        . "{$payStatus}\n\n"
        . "🚚 Доставка: " . ($service ? $service['NAME'] : "Неизвестно") . "\n"
//        . ($cdek ? "🚚 CDEK_UUID: " . $cdek : "") . "\n"
        . "🏠 Адрес доставки: {$deliveryAddress}\n\n"
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

    $message .= "📦 Товары: {$itemsList}";

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

        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,

        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,

        // 👇 ПРОКСИ
        CURLOPT_PROXY => '123.123.123.123:1080',
        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5,
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
        file_put_contents($logPath, "\n[TELEGRAM RESPONSE] HTTP {$httpCode}: {$response}\n", FILE_APPEND);
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
        file_put_contents($logPath, "\n[OK] Флаг SEND_TELEGRAM установлен для заказа {$orderId}\n", FILE_APPEND);
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

function afterOrderCreate(Event $event)
{
    Loader::includeModule('sale');
    Loader::includeModule('highloadblock');

    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    if (!$order) {
        return;
    }

    $orderId = $order->getId();
    $propertyCollection = $order->getPropertyCollection();
    $currency = $order->getCurrency();

    $getProp = static function ($propertyCollection, $propId) {
        $prop = $propertyCollection->getItemByOrderPropertyId($propId);
        return $prop ? trim((string)$prop->getValue()) : '';
    };

    /*
     * Удаляем группу 8 после оформления заказа
     */
    $userId = $order->getUserId();
    if ($userId) {
        $user = new CUser();
        $userGroups = CUser::GetUserGroup($userId);

        if (in_array(8, $userGroups)) {
            $newGroups = array_diff($userGroups, [8]);
            $user->SetUserGroup($userId, $newGroups);
        }
    }

    /*
     * Флаг отправки письма
     */
    $sendEmailProp = $propertyCollection->getItemByOrderPropertyId(29);
    if ($sendEmailProp && $sendEmailProp->getValue() === 'Y') {
        return;
    }

    /*
     * Проверка способа оплаты
     */
    $paymentCollection = $order->getPaymentCollection();
    $sendEmail = false;

    foreach ($paymentCollection as $paymentItem) {
        if ($paymentItem->getPaymentSystemId() == 8) { // 8 АльфаБанк / PayKeeper
            $sendEmail = true;
            break;
        }
    }

    if (!$sendEmail) {
        foreach ($paymentCollection as $paymentItem) {
            if ($paymentItem->getPaymentSystemId() != 8 && !$paymentItem->isPaid()) {
                $sendEmail = true;
                break;
            }
        }
    }

    if (!$sendEmail) {
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/local/log.txt',
            "\n[FAIL] Email не отправлен для заказа {$orderId}:  Другой статус\n",
            FILE_APPEND
        );
        return;
    }

    /*
     * Доставка
     */
    $deliveryIds = $order->getDeliverySystemId();
    $deliveryId = is_array($deliveryIds) && !empty($deliveryIds) ? $deliveryIds[0] : null;
    $deliveryName = 'Неизвестно';

    if ($deliveryId) {
        $service = Delivery\Services\Manager::getById($deliveryId);
        if ($service && !empty($service['NAME'])) {
            $deliveryName = $service['NAME'];
        }
    }

    /*
     * Данные клиента
     */
    $userName = trim($getProp($propertyCollection, 13) . ' ' . $getProp($propertyCollection, 14));
    $userEmail = $getProp($propertyCollection, 12);
    $userPhone = $getProp($propertyCollection, 15);

    if ($userEmail === '' && $userId) {
        $userData = CUser::GetByID($userId)->Fetch();
        if ($userData) {
            $userEmail = (string)$userData['EMAIL'];
            if ($userName === '') {
                $userName = trim($userData['NAME'] . ' ' . $userData['LAST_NAME']);
            }
        }
    }

    /*
     * Адрес
     */
    $city = $getProp($propertyCollection, 17);
    $street = $getProp($propertyCollection, 18);
    $home = $getProp($propertyCollection, 19);
    $apartment = $getProp($propertyCollection, 20);
    $addressCdek = $getProp($propertyCollection, 31);
    $numberCdek = $getProp($propertyCollection, 36);

    $parts = array_filter([$city, $street, $home, $apartment]);
    $address = implode(', ', $parts);
    $deliveryAddress = $address ?: $addressCdek;

    /*
     * Товары
     */
    $items = [];
    foreach ($order->getBasket()->getListOfFormatText() as $basketItem) {
        $items[] = html_entity_decode($basketItem);
    }
    $itemsList = implode("\n", preg_replace('/\[[^\]]*\]/u', '', $items));

    /*
     * Бонусы и способ оплаты
     */
    $bonusPaidAmount = 0;
    $typePayNames = [];

    foreach ($paymentCollection as $paymentItem) {
        $paySystemId = (int)$paymentItem->getPaymentSystemId();

        if ($paySystemId === 6) {
            if ($paymentItem->isPaid()) {
                $bonusPaidAmount += $paymentItem->getSum();
            }
            continue;
        }

        $payName = trim((string)$paymentItem->getPaymentSystemName());
        if ($payName === '') {
            $paySystem = $paymentItem->getPaySystem();
            if ($paySystem) {
                $payName = trim((string)$paySystem->getField('NAME'));
            }
        }

        if ($payName !== '') {
            $typePayNames[] = $payName;
        }
    }

    $typePayNames = array_values(array_unique($typePayNames));
    if ($typePayNames) {
        $typePay = implode(', ', $typePayNames);
    } elseif ($order->isPaid()) {
        $typePay = 'Оплата картой';
    } else {
        $typePay = 'Оплата при получении';
    }

    $orderPrice = (float)$order->getPrice();
    $amountToPay = $orderPrice - $bonusPaidAmount;
    $payStatus = $order->isPaid() ? 'Заказ оплачен' : 'Заказ не оплачен';

    /*
     * Промокоды и скидки
     */
    $coupons = DiscountCouponsManager::get(true);
    $promoCode = '';
    $promoDiscount = 0;

    foreach ($coupons as $coupon) {
        if ($coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
            $promoCode = $coupon['COUPON'];
            break;
        }
    }

    $discountData = $order->getDiscount()->getApplyResult(true);

    if ($promoCode === '' && !empty($discountData['COUPON_LIST'])) {
        foreach ($discountData['COUPON_LIST'] as $coupon) {
            $promoCode = is_array($coupon)
                ? (string)($coupon['COUPON'] ?? $coupon['COUPON_NUMBER'] ?? '')
                : (string)$coupon;
            if ($promoCode !== '') {
                break;
            }
        }
    }

    if (!empty($discountData['PRICES']['BASKET'])) {
        foreach ($discountData['PRICES']['BASKET'] as $item) {
            if (!empty($item['DISCOUNT'])) {
                $promoDiscount += $item['DISCOUNT'];
            }
        }
    }

    $promoDiscount = round($promoDiscount);
    $discountLabel = $promoCode !== '' ? $promoCode : 'не применён';
    $discountAmountLabel = $promoDiscount > 0
        ? $promoDiscount . ' ' . $currency
        : '0 ' . $currency;

    $bonusLabel = $bonusPaidAmount > 0
        ? $bonusPaidAmount . ' ' . $currency
        : '0 ' . $currency;

    $adminOrderUrl = 'https://vl28.pro/bitrix/admin/sale_order_view.php?ID=' . $orderId;

    $fields = [
        "ORDER_ID" => $orderId,
        "ORDER_TYPE" => $isNew ? "Новый заказ" : "Оплата заказа",
        "ITEMS" => $itemsList,
        "PRICE" => $orderPrice . ' ' . $currency,
        "AMOUNT" => $amountToPay . ' ' . $currency,
        "DISCOUNT" => $discountLabel,
        "DISCOUNT_AMOUNT" => $discountAmountLabel,
        "PROMO_CODE" => $promoCode,
        "TYPE_PAY" => $typePay,
        "PAY_METHOD" => $typePay,
        "PAY_STATUS" => $payStatus,
        "BONUS" => $bonusLabel,
        "USER_NAME" => $userName,
        "EMAIL" => $userEmail,
        "EMAIL_TO" => $userEmail,
        "PHONE" => $userPhone,
        "DELIVERY" => $deliveryName,
        "ADDRESS" => $deliveryAddress,
        "DELIVERY_NUMBER" => $numberCdek,
        "ADMIN_LINK" => $adminOrderUrl,
    ];

    $result = MailEvent::send([
        "EVENT_NAME" => "NEW_ORDER_NOTIFICATION",
        "LID" => $order->getSiteId() ?: "s1",
        "C_FIELDS" => $fields,
    ]);

    $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/log.txt';

    if ($result->isSuccess()) {
        if ($sendEmailProp) {
            $sendEmailProp->setValue('Y');
            $order->save();
        }

        file_put_contents(
            $logPath,
            "\n[OK] Email отправлен для заказа {$orderId}\n" . print_r($fields, true) . "\n",
            FILE_APPEND
        );
    } else {
        file_put_contents(
            $logPath,
            "\n[FAIL] Email не отправлен для заказа {$orderId}: " .
            implode(', ', $result->getErrorMessages()) . "\n" .
            print_r($fields, true) . "\n",
            FILE_APPEND
        );
    }
}

function onOrderCancel(Event $event)
{
    Loader::includeModule('sale');

    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    if (!$order || $isNew) {
        return;
    }

    if ($order->getField('STATUS_ID') !== 'C') {
        return;
    }

    if (!$order->getFields()->isChanged('STATUS_ID')) {
        return;
    }

    $accountNumber = (string)$order->getField('ACCOUNT_NUMBER');
    if ($accountNumber === '') {
        $accountNumber = (string)$order->getId();
    }

    $orderId = $order->getId();
    $lid = $order->getSiteId() ?: 's1';
    $propertyCollection = $order->getPropertyCollection();
    $paymentCollection = $order->getPaymentCollection();
    $currency = $order->getCurrency();

    $getProp = static function ($propertyCollection, $propId) {
        $prop = $propertyCollection->getItemByOrderPropertyId($propId);
        return $prop ? trim((string)$prop->getValue()) : '';
    };

    $userName = trim($getProp($propertyCollection, 13) . ' ' . $getProp($propertyCollection, 14));
    $userEmail = $getProp($propertyCollection, 12);
    $userPhone = $getProp($propertyCollection, 15);

    if ($userEmail === '' && $order->getUserId()) {
        $user = CUser::GetByID($order->getUserId())->Fetch();
        if ($user) {
            $userEmail = (string)$user['EMAIL'];
            if ($userName === '') {
                $userName = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
            }
        }
    }

    $city = $getProp($propertyCollection, 17);
    $street = $getProp($propertyCollection, 18);
    $home = $getProp($propertyCollection, 19);
    $apartment = $getProp($propertyCollection, 20);
    $addressCdek = $getProp($propertyCollection, 31);
    $numberCdek = $getProp($propertyCollection, 36);
    $deliveryAddress = implode(', ', array_filter([$city, $street, $home, $apartment])) ?: $addressCdek;

    $deliveryIds = $order->getDeliverySystemId();
    $deliveryId = is_array($deliveryIds) && !empty($deliveryIds) ? $deliveryIds[0] : null;
    $deliveryName = 'Неизвестно';
    if ($deliveryId) {
        $service = Delivery\Services\Manager::getById($deliveryId);
        if ($service && !empty($service['NAME'])) {
            $deliveryName = $service['NAME'];
        }
    }

    $items = [];
    foreach ($order->getBasket()->getListOfFormatText() as $basketItem) {
        $items[] = html_entity_decode($basketItem);
    }
    $itemsList = implode("\n", preg_replace('/\[[^\]]*\]/u', '', $items));

    $bonusPaidAmount = 0;
    $payMethodNames = [];
    foreach ($paymentCollection as $paymentItem) {
        if ($paymentItem->getPaymentSystemId() == 6 && $paymentItem->isPaid()) {
            $bonusPaidAmount += $paymentItem->getSum();
        }
        $paySystem = $paymentItem->getPaySystem();
        if ($paySystem) {
            $payMethodNames[] = $paySystem->getField('NAME');
        }
    }
    $payMethodNames = array_unique(array_filter($payMethodNames));
    $amount = $order->getPrice() - $bonusPaidAmount;
    $payStatus = $order->isPaid() ? 'Заказ оплачен' : 'Заказ не оплачен';
    $payMethod = $payMethodNames
        ? implode(', ', $payMethodNames)
        : ($order->isPaid() ? 'Оплата онлайн' : 'Оплата при получении');

    $coupons = DiscountCouponsManager::get(true);
    $promoCode = '';
    $promoDiscount = 0;
    foreach ($coupons as $coupon) {
        if ($coupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED) {
            $promoCode = $coupon['COUPON'];
            break;
        }
    }

    $discountData = $order->getDiscount()->getApplyResult(true);
    if (!empty($discountData['PRICES']['BASKET'])) {
        foreach ($discountData['PRICES']['BASKET'] as $item) {
            if (!empty($item['DISCOUNT'])) {
                $promoDiscount += $item['DISCOUNT'];
            }
        }
    }
    $promoDiscount = round($promoDiscount);
    $discountsText = $promoCode ? "{$promoDiscount} {$currency}" : 'Промокод не применён';

    $orderDate = '';
    $dateInsert = $order->getDateInsert();
    if ($dateInsert) {
        $orderDate = $dateInsert->toString();
    }

    $cancelDescription = trim((string)$order->getField('REASON_CANCELED'));
    if ($cancelDescription === '') {
        $cancelDescription = 'Отменён покупателем';
    }

    $site = CSite::GetByID($lid)->Fetch() ?: [];
    $serverName = $site['SERVER_NAME']
        ?: Option::get('main', 'server_name', '')
        ?: ($_SERVER['SERVER_NAME'] ?? 'vl28.pro');
    $siteName = $site['SITE_NAME']
        ?: $site['NAME']
        ?: Option::get('main', 'site_name', '')
        ?: $serverName;
    $saleEmail = Option::get('sale', 'order_email', '');
    if ($saleEmail === '') {
        $saleEmail = 'order@' . $serverName;
    }
    $defaultEmailFrom = Option::get('main', 'email_from', '');
    if ($defaultEmailFrom === '') {
        $defaultEmailFrom = $saleEmail;
    }

    $publicUrl = '';
    if (class_exists(OrderHelper::class) && OrderHelper::isAllowGuestView($order)) {
        $publicUrl = (string)OrderHelper::getPublicLink($order);
    }
    if ($publicUrl === '') {
        $publicUrl = 'https://' . $serverName . '/profile/orders/';
    }

    $adminOrderUrl = 'https://' . $serverName . '/bitrix/admin/sale_order_view.php?ID=' . $orderId;

    $fields = [
        "ORDER_ID" => $accountNumber,
        "ORDER_ACCOUNT_NUMBER_ENCODE" => urlencode(urlencode($accountNumber)),
        "ORDER_REAL_ID" => $orderId,
        "ORDER_DATE" => $orderDate,
        "EMAIL" => $userEmail,
        "EMAIL_TO" => $userEmail,
        "ORDER_CANCEL_DESCRIPTION" => $cancelDescription,
        "ORDER_PUBLIC_URL" => $publicUrl,
        "SALE_EMAIL" => $saleEmail,
        "DEFAULT_EMAIL_FROM" => $defaultEmailFrom,
        "SITE_NAME" => $siteName,
        "SERVER_NAME" => $serverName,

        "USER_NAME" => $userName,
        "PHONE" => $userPhone,
        "ADDRESS" => $deliveryAddress,
        "DELIVERY" => $deliveryName,
        "PAY_STATUS" => $payStatus,
        "PAY_METHOD" => $payMethod,
        "PRICE" => $amount . ' ' . $currency,
        "AMOUNT" => $order->getPrice() . ' ' . $currency,
        "DISCOUNT" => $discountsText,
        "PROMO_CODE" => $promoCode,
        "BONUS" => $bonusPaidAmount,
        "ITEMS" => $itemsList,
        "ADMIN_LINK" => $adminOrderUrl,
        "DELIVERY_NUMBER" => $numberCdek,
    ];

    $result = MailEvent::send([
        "EVENT_NAME" => "CANCEL_ORDER",
        "LID" => $lid,
        "C_FIELDS" => $fields,
    ]);

    $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/log.txt';

    if ($result->isSuccess()) {
        file_put_contents(
            $logPath,
            "\n[OK] CANCEL_ORDER email отправлен для заказа {$orderId}\n" .
            print_r($fields, true) . "\n",
            FILE_APPEND
        );
    } else {
        file_put_contents(
            $logPath,
            "\n[FAIL] CANCEL_ORDER email не отправлен для заказа {$orderId}: " .
            implode(', ', $result->getErrorMessages()) . "\n" .
            print_r($fields, true) . "\n",
            FILE_APPEND
        );
    }
}