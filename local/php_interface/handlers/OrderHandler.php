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

