<?php
function sendNewsToSubscribers(&$arFields)
{
    // Только инфоблок 3
    if ($arFields['IBLOCK_ID'] != 3 || $arFields['RESULT'] !== true)
        return;

    if (!\Bitrix\Main\Loader::includeModule('subscribe'))
        return;

    // Получаем подписчиков
    $rsSub = \CSubscription::GetList(
        [],
        [
            'ACTIVE' => 'Y',
            'CONFIRMED' => 'Y',
        ]
    );

    while ($sub = $rsSub->Fetch())
    {

        \CEvent::Send(
            'SEND_EMAIL_TO_SUBSCRIBERS',
            's1',
            [
                'EMAIL_TO'    => $sub['EMAIL'],
                'NAME'        => $arFields['NAME'],
                'DETAIL_URL'  => 'https://' . $_SERVER['SERVER_NAME'] .'/news/'. $arFields['CODE'].'/',
            ]
        );
    }
}