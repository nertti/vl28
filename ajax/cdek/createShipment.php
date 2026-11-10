<?php
$data = [
    "type" => 1,
    "number" => $orderId,
    "tariff_code" => $cdekTariff,
    "recipient" => [
        "name" => "$surname $name",
        "phones" => [["number" => $phone]]
    ],
    "from_location" => ["code" => 44],
    "to_location" => ["code" => $cityCode],
    "packages" => [[
        "number" => "1",
        "weight" => 1200,
        "length" => 30,
        "width" => 20,
        "height" => 10,
        "items" => []
    ]]
];
