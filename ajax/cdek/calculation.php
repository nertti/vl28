<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helper.php';

header('Content-Type: application/json; charset=utf-8');

// -------------------------
// Входные параметры
// -------------------------
$cityCodeTo   = (int)($_GET['city_code'] ?? 0);
$officeCode  = trim($_GET['office_code'] ?? ''); // может быть пустым
$cityCodeFrom = 44; // склад (Москва, пример)

if ($cityCodeTo <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'Не указан код города СДЭК'
    ]);
}

// -------------------------
// Авторизация
// -------------------------
try {
    $token = getCdekToken();
} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Ошибка авторизации СДЭК',
        'error' => $e->getMessage()
    ]);
}

// -------------------------
// Габариты (ПОКА тестовые)
// потом спокойно возьмёшь из корзины
// -------------------------
$packages = [
    [
        'weight' => 1000, // граммы
        'length' => 10,
        'width'  => 10,
        'height' => 10,
    ]
];

// -------------------------
// Тарифы
// -------------------------
$tariffs = [
    // 🚚 Курьер
    'courier' => [
        'title' => 'Курьером в руки',
        'tariff_code' => 137,
        'type' => 'courier',
    ],
    'courier_express' => [
        'title' => 'Курьером в руки (экспресс)',
        'tariff_code' => 139,
        'type' => 'courier',
    ],

    // 🏢 ПВЗ
    'pvz' => [
        'title' => 'Самовывоз из ПВЗ',
        'tariff_code' => 136,
        'type' => 'pvz',
    ],
    'pvz_express' => [
        'title' => 'Самовывоз из ПВЗ (экспресс)',
        'tariff_code' => 138,
        'type' => 'pvz',
    ],
];

$result = [];

// -------------------------
// Расчёт
// -------------------------
foreach ($tariffs as $key => $tariff) {

    // ❌ ПВЗ без выбранного офиса — не считаем
    if ($tariff['type'] === 'pvz' && $officeCode === '') {
        $result[$key] = [
            'available' => false,
            'title' => $tariff['title'],
            'reason' => 'Не выбран пункт выдачи',
            'tariff_code' => $tariff['tariff_code'],
        ];
        continue;
    }

    $payload = [
        'tariff_code' => $tariff['tariff_code'],
        'from_location' => [
            'code' => $cityCodeFrom,
        ],
        'to_location' => [
            'code' => $cityCodeTo,
        ],
        'packages' => $packages,
    ];

    // ✅ если ПВЗ — добавляем office_code
    if ($tariff['type'] === 'pvz') {
        $payload['to_location']['office_code'] = $officeCode;
    }

    try {
        $response = curlRequest(
            'https://api.cdek.ru/v2/calculator/tariff',
            $payload,
            'POST',
            [
                "Authorization: Bearer {$token}"
            ]
        );

        $data = json_decode($response, true);

        if (!is_array($data)) {
            throw new Exception('Некорректный ответ СДЭК');
        }

        if (!empty($data['errors'])) {
            $result[$key] = [
                'available' => false,
                'title' => $tariff['title'],
                'error' => $data['errors'][0]['message'] ?? 'Ошибка расчёта',
                'tariff_code' => $tariff['tariff_code'],
            ];
            continue;
        }

        $result[$key] = [
            'available'   => true,
            'title'       => $tariff['title'],
            'price'       => (float)$data['delivery_sum'],
            'period_min'  => $data['period_min'] ?? null,
            'period_max'  => $data['period_max'] ?? null,
            'tariff_code' => $tariff['tariff_code'],
        ];

    } catch (Throwable $e) {
        $result[$key] = [
            'available' => false,
            'title' => $tariff['title'],
            'error' => $e->getMessage(),
            'tariff_code' => $tariff['tariff_code'],
        ];
    }
}

// -------------------------
// Ответ
// -------------------------
jsonResponse([
    'success' => true,
    'city_code' => $cityCodeTo,
    'office_code' => $officeCode ?: null,
    'deliveries' => $result,
]);
