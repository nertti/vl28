<?php
// Подключаем необходимые файлы
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("Sale");
Bitrix\Main\Loader::includeModule("Catalog");
try {
    // Получаем данные из тела запроса
    $requestData = json_decode(file_get_contents('php://input'), true);

    // Проверяем наличие всех необходимых данных
    if (!isset($requestData['id'])) {
        throw new Exception('Отсутствуют необходимые параметры');
    }

    // Проверяем корректность значений
    $id = intval($requestData['id']);

    if ($id <= 0) {
        throw new Exception('Неверные значения параметров');
    }

    $fUserId = $requestData['fUserId'];
    $siteId = $requestData['siteId'];
    $basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

    foreach ($basket as $basketItem) {
        if ($basketItem->getField('ID') == $id){
            $basketItem->delete();
            $basket->save();
        }
    }

    // Формируем ответ
    $response = [
        'status' => 'success',
        'message' => 'Товар удалён',
        'id' => $id,
        'totalPrice' => $basket->getPrice(),
        'count' => count($basket),
    ];

} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

// Устанавливаем заголовок ответа
header('Content-Type: application/json; charset=utf-8');

// Выводим ответ
echo json_encode($response);

// Подключаем эпилог
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");