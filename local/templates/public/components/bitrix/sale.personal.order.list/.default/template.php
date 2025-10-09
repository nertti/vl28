<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var CBitrixPersonalOrderListComponent $component */
/** @var array $arParams */
/** @var array $arResult */

//pr($arResult['ORDERS']);
?>

<div class="account__default account__default_borderless">
    <div class="account__orders">
        <div class="account__list">
            <?php if(!empty($arResult['ORDERS'])):?>
            <?php foreach ($arResult['ORDERS'] as $key => $order):?>
                <?php if ($order['ORDER']['PRICE'] != 0): ?>
                    <?php //pr($order['BASKET_ITEMS']) ?>
                    <div class="account__order">
                        <p class="account__order-title">Заказ #<?= $order['ORDER']['ID'] ?></p>
                        <div class="account__order-wrap">
                            <div class="account__product account__product_space">
                                <?php
                                $firstElement = reset($order['BASKET_ITEMS']);
                                $productId = $firstElement['PRODUCT_ID'];
                                $element = CIBlockElement::GetByID($productId)->GetNext();
                                $previewImage = CFile::GetFileArray($element['PREVIEW_PICTURE']);
                                ?>
                                <img src="<?=$previewImage['SRC']?>" alt="">
                                <div class="account__product-inner">
                                    <div class="account__product-param">
                                        <strong>Дата</strong>
                                        <p><?= $order['ORDER']['DATE_INSERT_FORMATED'] ?>.</p>
                                    </div>
                                    <div class="account__product-param">
                                        <strong>Товаров</strong>
                                        <p><?= count($order['BASKET_ITEMS']) ?> шт.</p>
                                    </div>
                                    <div class="account__product-param">
                                        <strong>Сумма</strong>
                                        <p><?= floor($order['ORDER']['PRICE']) ?> ₽ </p>
                                    </div>
                                    <div class="account__product-param">
                                        <strong>Статус</strong>
                                        <p>
                                            <?php if ($order['ORDER']['STATUS_ID'] == 'N'): ?>
                                                Принят, ожидается оплата
                                            <?php elseif ($order['ORDER']['STATUS_ID'] == 'D'): ?>
                                                Принят, ожидается доставка
                                            <?php elseif ($order['ORDER']['STATUS_ID'] == 'F'): ?>
                                                Выполнено
                                            <?php elseif ($order['ORDER']['STATUS_ID'] == 'P'): ?>
                                                Оплачено
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <a href="<?= htmlspecialcharsbx($order["ORDER"]["URL_TO_DETAIL"]) ?>"
                                       class="black-btn">Больше информации</a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php else:?>На данный момент у Вас нет заказов
            <?php endif;?>
        </div>
    </div>
</div>