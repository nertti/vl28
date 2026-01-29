<?php
/**
 * Получение и кэширование OAuth-токена СДЭК
 */

if (!defined('B_PROLOG_INCLUDED')) {
    define('B_PROLOG_INCLUDED', true);
}

const CDEK_CLIENT_ID     = 'JwBlVbsxsKlPtt7Jc5JZy80dWCrrrmce';
const CDEK_CLIENT_SECRET = 'jHVHyGZYWn0CVABsNPSCIqNM2hahKLBc';
const CDEK_TOKEN_FILE   = '/upload/cdek_token.json';
const CDEK_API_URL      = 'https://api.cdek.ru';

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

/**
 * Получить access_token СДЭК
 */
function getCdekToken(): string
{
    $file = $_SERVER['DOCUMENT_ROOT'] . CDEK_TOKEN_FILE;

    // 1️⃣ пробуем взять из кэша
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data['access_token']) && $data['expires_at'] > time()) {
            return $data['access_token'];
        }
    }

    // 2️⃣ запрашиваем новый токен
    $url = CDEK_API_URL . '/v2/oauth/token';

    $response = curlRequest(
        $url . '?grant_type=client_credentials'
        . '&client_id=' . CDEK_CLIENT_ID
        . '&client_secret=' . CDEK_CLIENT_SECRET,
        [],
        'POST'
    );

    $data = json_decode($response, true);

    if (empty($data['access_token'])) {
        throw new Exception('Не удалось получить токен СДЭК');
    }

    // 3️⃣ сохраняем
    file_put_contents($file, json_encode([
        'access_token' => $data['access_token'],
        'expires_at'   => time() + (int)$data['expires_in'] - 60,
    ], JSON_UNESCAPED_UNICODE));

    return $data['access_token'];
}
