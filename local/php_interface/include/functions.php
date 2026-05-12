<?php

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\Web\WebP;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

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

    $arSelect = Array(
        'ID',
        'IBLOCK_ID',
        'NAME',
        'PROPERTY_CONDITIONS',
    );
    $arFilter = Array("IBLOCK_ID"=>IntVal(21), "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    $arCards = [];
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arCards[] = $arFields;
    }

    // Определяем карту по сумме
    $cardId = null;
    if ($summary < $arCards[1]['PROPERTY_CONDITIONS_VALUE']) {
        $cardId = 10;
    } elseif ($summary < $arCards[2]['PROPERTY_CONDITIONS_VALUE']) {
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

function generateRandomString(): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 8; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Функция проверки уникальности ссылки в базе
function isLinkUnique($link) {
    $res = CUser::GetList(array(), array(), ["UF_REFERRAL_LINK" => $link], ["FIELDS" => ["ID"]]);
    return !($res->Fetch());
}

function onAfterUserUpdateHandler(&$arFields)
{
    // 1. Определяем ID пользователя (учитываем оба события)
    $userId = (int)($arFields['ID'] ?: $arFields['USER_ID']);

    if ($userId <= 0) return;

    static $isRunning = false;
    if ($isRunning) return;

    // Получаем текущие данные пользователя
    $dbUser = CUser::GetList(array(), array(), ["ID" => $userId], ["SELECT" => ["UF_REFERRAL_LINK", "UF_LINK_PARTNER"]]);
    $arUser = $dbUser->Fetch();

    $fieldsToUpdate = [];

    // 2. Генерируем СВОЮ ссылку, если её еще нет
    if (empty($arUser['UF_REFERRAL_LINK'])) {
        do {
            $link = generateRandomString();
        } while (!isLinkUnique($link));
        $fieldsToUpdate['UF_REFERRAL_LINK'] = $link;
    }

    // 4. Обновляем, если есть что обновлять
    if (!empty($fieldsToUpdate)) {
        $isRunning = true;
        $cUser = new CUser;
        $cUser->Update($userId, $fieldsToUpdate);
        $isRunning = false;
    }

    if (function_exists('RecalculateUserSummaryPay')) {
        RecalculateUserSummaryPay($userId);
    }
}
function OnLoyaltyCardChanged(&$arFields)
{
    // Проверяем, что событие относится к нужному инфоблоку
    if (!isset($arFields['IBLOCK_ID']) || (int)$arFields['IBLOCK_ID'] !== 21)
        return;

    // Получаем всех активных пользователей
    $rsUsers = CUser::GetList(
        ($by = "id"),
        ($order = "asc"),
        ["ACTIVE" => "Y"],
        ["FIELDS" => ["ID"]]
    );

    while ($arUser = $rsUsers->Fetch()) {
        $userId = (int)$arUser["ID"];

        if ($userId > 0 && function_exists('recalculateUserSummaryPay')) {
            recalculateUserSummaryPay($userId);
        }
    }
}

function getResponsiveImage($fileId)
{
    if (!$fileId) return null;

    // 3:4 (W:H)
    $sizesMap = [
        300 => 400,
        450 => 600,
        600 => 800,
        750 => 1000,
    ];

    $srcset = [];
    $default = null;

    foreach ($sizesMap as $w => $h) {
        $img = CFile::ResizeImageGet(
            $fileId,
            ['width' => $w, 'height' => $h],
            BX_RESIZE_IMAGE_EXACT,
            true
        );

        if (!$default) {
            $default = $img['src'];
        }

        $srcset[] = $img['src'] . " {$w}w";
    }

    return [
        'src' => $default,
        'srcset' => implode(', ', $srcset),
        'sizes' => '(max-width: 768px) 90vw, (max-width: 1200px) 45vw, 450px'
    ];
}