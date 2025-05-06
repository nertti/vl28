<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

/** @global CMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateFolder */

?>

<?php
//pr($arResult);
?>
<div class="account__default account__default_borderless">
    <div class="account__orders">
        <div class="account__list">
            <div class="account__order account__order_big">
                <p class="account__order-title">Заказ #<?=$arResult['ID']?></p>
                <div class="account__order-wrap">
                    <?php foreach ($arResult['BASKET'] as $item):?>
                        <?php
                        $productId = $item['PRODUCT_ID'];
                        $element = CIBlockElement::GetByID($productId)->GetNext();
                        $previewImage = CFile::GetFileArray($element['PREVIEW_PICTURE']);
                        ?>
                    <div class="account__product">
                        <div class="product">
                            <img src="<?=$previewImage['SRC']?>" alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            <div class="product__inner">
                                <p class="product__title"><?=$item['NAME']?></p>
                                <p class="product__price"><?=$item['BASE_PRICE']?> ₽</p>
                            </div>
                        </div>
                        <div class="account__product-params">
                            <div class="account__product-param">
                                <strong>Дата</strong>
                                <p>17 апреля 2024г.</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Сумма</strong>
                                <p><?=$item['BASE_PRICE']?> ₽</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Товаров</strong>
                                <p><?=count($arResult['BASKET'])?> шт.</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Сумма</strong>
                                <p><?=$item['BASE_PRICE']?> ₽ </p>
                            </div>
                            <div class="account__product-param">
                                <strong>Статус</strong>
                                <p><?=$arResult['STATUS']['NAME']?></p>
                            </div>
                            <!--
                            <div class="account__product-param">
                                <strong>Размер</strong>
                                <p>S</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Цвет</strong>
                                <div class="account__product-param_color">
                                    <span style="background: #000;"></span>
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
</div>