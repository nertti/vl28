<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Sale;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Loader::includeModule("sale");

// Загружаем корзину текущего пользователя
$basket = Sale\Basket::loadItemsForFUser(
    Sale\Fuser::getId(),
    Bitrix\Main\Context::getCurrent()->getSite()
);

// Получаем количество товаров (с учётом количества каждого)
$totalQuantity = 0;
foreach ($basket as $item) {
    $totalQuantity += $item->getQuantity();
}

$arResult["BASKET"] = $totalQuantity;
