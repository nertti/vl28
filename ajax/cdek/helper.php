<?php

/**
 * Вспомогательные функции для СДЭК
 */

if (!defined('B_PROLOG_INCLUDED')) {
    define('B_PROLOG_INCLUDED', true);
}

/**
 * Универсальный CURL-запрос
 */
function curlRequest(
    string $url,
    array  $data = [],
    string $method = 'POST',
    array  $headers = []
): string
{
    $ch = curl_init($url);

    $defaultHeaders = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
        CURLOPT_TIMEOUT => 15,
    ]);

    if ($method !== 'GET' && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('CURL ошибка: ' . $error);
    }

    curl_close($ch);

    // логируем ошибки API
    if ($httpCode >= 400) {
        cdekLog('HTTP ' . $httpCode . ' | ' . $url . ' | ' . $response);
    }

    return $response;
}

/**
 * Логирование СДЭК
 */
function cdekLog(string $message): void
{
    $file = $_SERVER['DOCUMENT_ROOT'] . '/upload/cdek.log';
    file_put_contents(
        $file,
        '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
        FILE_APPEND
    );
}

/**
 * Безопасный JSON-ответ
 */
function jsonResponse(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
