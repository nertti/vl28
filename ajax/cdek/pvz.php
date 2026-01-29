<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__.'/auth.php';
require_once __DIR__.'/helper.php';

header('Content-Type: application/json');

$cityCode = (int)($_GET['city_code'] ?? 0);

if ($cityCode <= 0) {
    echo json_encode(['success' => false, 'message' => 'Не указан город']);
    exit;
}

$token = getCdekToken();

/**
 * deliverypoints:
 * можно фильтровать по городу, типу (PVZ), активности
 */
$url = 'https://api.cdek.ru/v2/deliverypoints?'
    . http_build_query([
        'city_code' => $cityCode,
        'type' => 'PVZ',
        'have_cashless' => 1,
        'is_handout' => 1
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

foreach ($data as $pvz) {
    $result[] = $pvz;
}

echo json_encode([
    'success' => true,
    'items' => $result
]);
