<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |    Attention!
 * |    The following comments are for system use
 * |    and are required for the component to work correctly in ajax mode:
 * |    <!-- items-container -->
 * |    <!-- pagination-container -->
 * |    <!-- component-end -->
 */

$this->setFrameMode(true);
?>

<?php if (!empty($arResult["ITEMS"])): ?>
    <div class="account__favorite">
        <?php foreach ($arResult["ITEMS"] as $cell => $arElement): ?>
            <?php
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
            ?>
            <?php
            $res = CIBlockSection::GetByID($arElement['IBLOCK_SECTION_ID']);
            $arRes = $res->GetNext();
            ?>
            <div id="<?= $this->GetEditAreaId($arElement['ID']); ?>" class="product">
                    <span
                            class="product__favorite product__favorite_active favor active pointer"
                            data-item="<?= $arElement['ID'] ?>"
                            data-name="<?= htmlspecialcharsbx($arElement['NAME']) ?>"
                            data-image="<?= $arElement['DETAIL_PICTURE']['SRC'] ?>"
                    ></span>
                <img src="<?= $arElement['DETAIL_PICTURE']['SRC'] ?>"
                     alt="<?= $arElement['NAME'] ?>"
                     class="product__img">
                <a href="/catalog/<?= $arRes['CODE'] ?>/<?= $arElement['CODE'] ?>/" class="product__inner">
                    <p class="product__title"><?= $arElement['NAME'] ?></p>
                    <?php if($arElement['OFFERS'][0]['PRICES']['BASE']['VALUE'] == $arElement['OFFERS'][0]['PRICES']['BASE']['DISCOUNT_VALUE']):?>
                        <p class="product__price"><?= $arElement['OFFERS'][0]['PRICES']['BASE']['PRINT_VALUE'] ?></p>
                    <?php else:?>
                        <div class="price_wrapper">
                            <p class="product__price"><?= $arElement['OFFERS'][0]['PRICES']['BASE']['PRINT_DISCOUNT_VALUE'] ?></p>
                            <p class="tovar__price__wishoout__discont"><?= $arElement['OFFERS'][0]['PRICES']['BASE']['PRINT_VALUE'] ?></p>
                        </div>
                    <?php endif;?>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="hystmodal" id="delFavoriteModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
                <button data-hystclose class="hystmodal__close"></button>

                <div class="thanks thanks-product">
                    <div class="thanks-product__image">
                        <img src="" alt="" id="delFavoriteImage">
                    </div>

                    <p class="h2">Товар удалён из избранного!</p>

                    <p id="delFavoriteName"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="favorites-empty" style="display:none;">
        <p class="h2">В вашем избранном пока пусто.</p>
        <p>Чтобы добавить товары в избранное, отметьте его значком сердца <span class="favorite-btn" style="display: inline-flex"></span>. Это можно сделать как на странице товара, так и прямо из вашей корзины с покупками.</p>
    </div>

<?php else: ?>

    <div class="favorites-empty">
        <p class="h2">В вашем избранном пока пусто.</p>
        <p>Чтобы добавить товары в избранное, отметьте его значком сердца <span class="favorite-btn" style="display: inline-flex"></span>. Это можно сделать как на странице товара, так и прямо из вашей корзины с покупками.</p>
    </div>
<?php endif; ?>