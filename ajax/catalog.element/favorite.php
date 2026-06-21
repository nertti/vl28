<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $APPLICATION;
global $USER;

header('Content-Type: application/json');

$result = [
    'success' => false,
    'action' => null,
    'count' => 0,
];

$productId = (int)($_POST['id'] ?? 0);

if ($productId <= 0) {
    echo json_encode($result);
    die();
}

if (!$USER->IsAuthorized()) {

    $favorites = unserialize($APPLICATION->get_cookie('favorites'));

    if (!is_array($favorites)) {
        $favorites = [];
    }

    if (!in_array($productId, $favorites)) {

        $favorites[] = $productId;

        $result['success'] = true;
        $result['action'] = 'add';

    } else {

        $key = array_search($productId, $favorites);

        if ($key !== false) {
            unset($favorites[$key]);
        }

        $favorites = array_values($favorites);

        $result['success'] = true;
        $result['action'] = 'delete';
    }

    setcookie(
        'BITRIX_SM_favorites',
        serialize($favorites),
        time() + 60 * 60 * 24 * 60,
        '/',
        'vl28.pro'
    );

    $result['count'] = count($favorites);

} else {

    $userId = (int)$USER->GetID();

    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();

    $favorites = $arUser['UF_FAVORITES'];

    if (!is_array($favorites)) {
        $favorites = [];
    }

    if (!in_array($productId, $favorites)) {

        $favorites[] = $productId;

        $result['success'] = true;
        $result['action'] = 'add';

    } else {

        $key = array_search($productId, $favorites);

        if ($key !== false) {
            unset($favorites[$key]);
        }

        $favorites = array_values($favorites);

        $result['success'] = true;
        $result['action'] = 'delete';
    }

    $USER->Update($userId, [
        'UF_FAVORITES' => $favorites
    ]);

    $result['count'] = count($favorites);
}

echo json_encode($result);
die();