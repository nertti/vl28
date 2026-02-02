<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__. '/helper.php';


/**
 * Создание отправления СДЭК
 *
 * @param array $orderData
 * @return array
 * @throws Exception
 */
function createCdekOrder(array $orderData): array
{
    $token = getCdekToken();

    $url = CDEK_API_URL . '/v2/orders';

    $payload = [
        'type' => 1, // интернет-магазин
        'number' => (string)$orderData['order_number'], // номер заказа в Bitrix
        'tariff_code' => (int)$orderData['tariff_code'],

        'sender' => [
            'company' => 'vl28.pro',
            'name' => 'Леонов Виталий Викторович',
            'phones' => [
                ['number' => '89265604088'],
            ],
        ],

        'recipient' => [
            'name' => $orderData['recipient_name'],
            'phones' => [
                ['number' => $orderData['recipient_phone']],
            ],
        ],

        // ===== КУДА ДОСТАВЛЯЕМ =====
        'to_location' => $orderData['to_location'],

        // ===== ОТКУДА ОТПРАВЛЯЕМ =====
        'from_location' => [
            'code' => 44, // код города отправителя (пример: Москва)
            'address' => 'Москва, Варшавское шоссе, 26 с32',

        ],

        // ===== МЕСТА =====
        'packages' => [
            [
                'number' => '1',
                'weight' => $orderData['weight'], // в граммах
                'width' => 30,
                'height' => 20,
                'length' => 40,
                'items' => $orderData['items'],
            ],
        ],
    ];

    // ===== ПВЗ (если самовывоз) =====
    if (!empty($orderData['pvz_code'])) {
        $payload['delivery_point'] = $orderData['pvz_code'];
    }

    $response = curlRequest(
        $url,
        $payload,
        'POST',
        [
            'Authorization: Bearer ' . $token,
        ]
    );

    $result = json_decode($response, true);

    if (!empty($result['errors'])) {
        cdekLog('CDEK ERROR: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
        throw new Exception('Ошибка создания отправления СДЭК');
    }

    return $result;
}
