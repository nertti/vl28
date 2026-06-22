<?php
// 1. Инициализация ядра Битрикса
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_NO_ACCELERATOR_RESET", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// 2. Импорт необходимых классов D7
use Bitrix\Main\Context;
use Bitrix\Main\Web\Cookie;

global $USER;

$result = [
    'success' => false,
    'action' => null,
    'count' => 0,
];

// Получаем объекты контекста, запроса и ответа
$context = Context::getCurrent();
$request = $context->getRequest();
$response = $context->getResponse();

// Безопасно получаем ID товара из POST-запроса
$productId = (int)($request->getPost('id') ?? 0);

if ($productId <= 0) {
    header('Content-Type: application/json');
    echo json_encode($result);
    die();
}

$favorites = [];

if (!$USER->IsAuthorized()) {
    // === НЕАВТОРИЗОВАННЫЙ ПОЛЬЗОВАТЕЛЬ (РАБОТА С КУКАМИ) ===

    // Читаем куку через D7 (Битрикс сам подставит префикс BITRIX_SM_)
    $favoritesCookie = $request->getCookie('favorites');
    
    // Декодируем из JSON формата
    $favorites = !empty($favoritesCookie) ? json_decode($favoritesCookie, true) : [];

    if (!is_array($favorites)) {
        $favorites = [];
    }

    // Логика добавления / удаления
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

    // Сохраняем обновленный массив обратно в куку в формате JSON
    $cookieValue = json_encode($favorites);
    $cookie = new Cookie('favorites', $cookieValue, time() + 5184000); // 60 дней
    $cookie->setPath('/');
    $cookie->setDomain('vl28.pro');
    $cookie->setHttpOnly(false); // Разрешаем чтение через JS

    $response->addCookie($cookie);

} else {
    // === АВТОРИЗОВАННЫЙ ПОЛЬЗОВАТЕЛЬ (РАБОТА С ПРОФИЛЕМ) ===

    $userId = (int)$USER->GetID();

    // Оптимизировано: получаем только нужное нам свойство UF_FAVORITES
    $arUser = CUser::GetList(
        'by', 'order', 
        ['ID' => $userId], 
        ['SELECT' => ['UF_FAVORITES']]
    )->Fetch();

    $favorites = $arUser['UF_FAVORITES'] ?? [];

    if (!is_array($favorites)) {
        $favorites = [];
    }

    // Логика добавления / удаления
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

    // Обновляем свойство в профиле пользователя
    $USER->Update($userId, [
        'UF_FAVORITES' => $favorites
    ]);
}

// Записываем количество элементов для ответа
$result['count'] = count($favorites);

// === ИСПРАВЛЕНИЕ ВЫВОДА ДЛЯ AJAX ===
header('Content-Type: application/json');

// Передаем JSON прямо в метод flush, чтобы Битрикс вывел его одновременно с куками
$response->flush(json_encode($result));
die();
