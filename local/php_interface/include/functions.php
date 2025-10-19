<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;

function pr($o, $show = false, $die = false, $fullBackTrace = false)
{
    global $USER;
    if ($USER->IsAdmin() || $show) {
        $bt = debug_backtrace();

        $firstBt = $bt[0];
        $dRoot = $_SERVER["DOCUMENT_ROOT"];
        $dRoot = str_replace("/", "\\", $dRoot);
        $firstBt["file"] = str_replace($dRoot, "", $firstBt["file"]);
        $dRoot = str_replace("\\", "/", $dRoot);
        $firstBt["file"] = str_replace($dRoot, "", $firstBt["file"]);
        ?>
        <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
            <div style='padding:3px 5px; background:#99CCFF;'>

                <? if ($fullBackTrace == false): ?>
                    File: <b><?= $firstBt["file"] ?></b> [line: <?= $firstBt["line"] ?>]
                <? else: ?>
                    <? foreach ($bt as $value): ?>
                        <?
                        $dRoot = str_replace("/", "\\", $dRoot);
                        $value["file"] = str_replace($dRoot, "", $value["file"]);
                        $dRoot = str_replace("\\", "/", $dRoot);
                        $value["file"] = str_replace($dRoot, "", $value["file"]);
//                        echo '<pre>';
//                        print_r($value);
//                        echo '</pre>';
                        ?>

                        File:
                        <b><?= $value["file"] ?></b> [line: <?= $value["line"] ?>] <?= $value['class'] . '->' . $value['function'] . '()' ?>
                        <br>
                    <? endforeach ?>
                <? endif; ?>
            </div>
            <pre style='padding:10px;'><? is_array($o) ? print_r($o) : print_r(htmlspecialcharsbx($o)) ?></pre>
        </div>
        <? if ($die == true) {
            die();
        } ?>
        <?
    } else {
        return false;
    }
}

function recalculateUserSummaryPay($userId)
{
    if ($userId <= 0) {
        return false;
    }

    if (!Loader::includeModule("sale")) {
        return false;
    }

    $userGroups = \Bitrix\Main\UserGroupTable::getList([
        'filter' => [
            '=USER_ID' => $userId,
            '=GROUP_ID' => 6,
        ],
        'select' => ['USER_ID']
    ])->fetch();

    if (!$userGroups) {
        // Если пользователь не состоит в группе ID = 6 — ничего не делаем
        return false;
    }

    // Считаем сумму оплаченных заказов
    $dbRes = Sale\Order::getList([
        'filter' => [
            'USER_ID' => $userId,
            'PAYED' => 'Y'
        ],
        'select' => ['PRICE']
    ]);

    $summary = 0;
    while ($arOrder = $dbRes->fetch()) {
        $summary += (float)$arOrder['PRICE'];
    }

    // Определяем карту по сумме
    $cardId = null;
    if ($summary < 75000) {
        $cardId = 10;
    } elseif ($summary >= 75000 && $summary < 150000) {
        $cardId = 11;
    } else {
        $cardId = 12;
    }

    // Обновляем поля пользователя
    $cUser = new CUser;
    $cUser->Update($userId, [
        'UF_SUMMARY_PAY' => $summary,
        'UF_CARD' => $cardId
    ]);

    return true;
}

function onAfterUserUpdateHandler(&$arFields)
{
    if (!isset($arFields['ID']) || (int)$arFields['ID'] <= 0) {
        return;
    }
    $userId = (int)$arFields['ID'];
    // Пересчёт суммы заказов и обновление UF_SUMMARY_PAY + UF_CARD
    RecalculateUserSummaryPay($userId);

}