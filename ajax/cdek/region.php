<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helper.php';

header('Content-Type: application/json');

$cityName = (string)($_GET['city_name'] ?? '');

if (!isset($cityName)) {
    echo json_encode(['success' => false, 'message' => 'Не указан город']);
    exit;
}

$token = getCdekToken();

/**
 * deliverypoints:
 * можно фильтровать по городу, типу (PVZ), активности
 */
$url = 'https://api.cdek.ru/v2/location/suggest/cities?'
    . http_build_query([
        'name' => ucfirst(strtolower($cityName)),
    ]);

$response = curlRequest(
    $url,
    [],
    'GET',
    [
        "Authorization: Bearer {$token}"
    ]
);

$data = json_decode($response, true);

if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Ошибка ответа СДЭК']);
    exit;
}

$result = [];

foreach ($data as $region) {

    $result[] = [
        'code' => $region['code'],
        'name' => $region['full_name'],
    ];
}

echo json_encode([
    'success' => true,
    'items' => $result
]);
