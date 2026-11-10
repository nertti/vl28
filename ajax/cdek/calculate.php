<?php
$token = getCdekToken();

$data = [
    "type" => 1,
    "from_location" => ["code" => 44], // Москва, например
    "to_location" => ["code" => (int)$_POST['city_code']],
    "packages" => [[
        "weight" => 1200, // граммы
        "length" => 30,
        "width" => 20,
        "height" => 10
    ]],
    "tariff_code" => 136 // ПВЗ / курьер / что выбрал
];

$response = curlRequest(
    'https://api.cdek.ru/v2/calculator/tariff',
    $data,
    'POST',
    [
        "Authorization: Bearer $token"
    ]
);

echo $response;
