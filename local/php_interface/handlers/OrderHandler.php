<?php

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

function onOrderPaid($order_id, $arFields)
{
    if ($arFields['PAYED'] == 'Y') {
        $bonus = $arFields['ORDER_PROP'][21];
        $userId = $arFields['USER_ID'];

        // Проверяем существование внутреннего счета пользователя
        $userBalance = CSaleUserAccount::GetByUserID($userId, "RUB");

        if (!$userBalance) {
            $arFields = array(
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => $bonus
            );
            $accountID = CSaleUserAccount::Add($arFields);
        } else {
            $arFields = array(
                "USER_ID" => $userId,
                "CURRENCY" => "RUB",
                "CURRENT_BUDGET" => $userBalance['CURRENT_BUDGET'] + $bonus,
            );
            $accountID = CSaleUserAccount::Update($userBalance['ID'], $arFields);
        }
    }
}

