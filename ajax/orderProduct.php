<?php
// Подключаем необходимые файлы
/** @var \CMain $USER */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("Sale");
Bitrix\Main\Loader::includeModule("Catalog");
try {

    // Получаем данные из тела запроса
    $requestData = json_decode(file_get_contents('php://input'), true);

    // Проверяем наличие всех необходимых данных
    if (!isset($requestData['id']) || !isset($requestData['count'])) {
        throw new Exception('Отсутствуют необходимые параметры');
    }

    // Проверяем корректность значений
    $id = intval($requestData['id']);
    $count = intval($requestData['count']);

    if ($id <= 0 || $count <= 0) {
        throw new Exception('Неверные значения параметров');
    }

    $fUserId = $requestData['fUserId'];
    $siteId = $requestData['siteId'];
    $basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);


    foreach ($basket as $basketItem) {
        if ($basketItem->getField('ID') == $id){
            $basketItem->setField('QUANTITY', $count); // Изменение поля
            $basket->save();

            $finalPrice = $basketItem->getFinalPrice();
        }
    }

    $fullPrice = $basket->getBasePrice();
    $salePrice = 0;

    if ($USER->isAuthorized()) {
        $userId = $USER->GetID();
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();

        if ($arUser['UF_CARD'] == 10) {
            $fullPrice = floor($fullPrice * 0.95);
            $salePrice = floor($fullPrice * 0.05);
        } elseif ($arUser['UF_CARD'] == 11) {
            $fullPrice = floor($fullPrice * 0.90);
            $salePrice = floor($fullPrice * 0.10);
        } elseif ($arUser['UF_CARD'] == 12) {
            $fullPrice = floor($fullPrice * 0.85);
            $salePrice = floor($fullPrice * 0.15);
        }
    }

    // Формируем ответ
    $response = [
        'status' => 'success',
        'message' => 'Количество товара обновлено',
        'id' => $id,
        'count' => $count,
        'price' => $finalPrice,
        'totalPrice' => $fullPrice,
        'salePrice' => $salePrice,
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