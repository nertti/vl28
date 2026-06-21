<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Context;

Loader::includeModule("sale");

// Загружаем корзину текущего пользователя
$basket = Basket::loadItemsForFUser(
    Fuser::getId(),
    Context::getCurrent()->getSite()
);

// Получаем количество товаров (с учётом количества каждого)
$totalQuantity = 0;
foreach ($basket as $item) {
    $totalQuantity += $item->getQuantity();
}
?>
<?php if ($totalQuantity > 0):?>
    <span class="header__cart-count"><?=$totalQuantity?></span>
<?php endif;?>