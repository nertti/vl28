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
 * @var string $templateFolder
 */

$this->setFrameMode(true);
//pr($arResult);
//pr($arResult['OFFERS'][0])
?>

<?php /** Начало карточки товара*/ ?>
    <section class="tovar">
        <div class="tovar__left">
            <div class="tovar__left-inner">
                <a href="/catalog/<?= $arResult['SECTION_CODE'] ?>" class="tovar__back">Назад</a>
                <span class="favorite-btn favor <?php if ($active): ?>active<?php endif; ?>"
                      data-item="<?= $arResult['ID'] ?>"></span>
            </div>
            <div class="swiper product-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>

                        <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                            <div class="swiper-slide  gallery-item">
                                <video
                                        data-index="0"
                                        class="catalog-cart-video"
                                        autoplay
                                        muted
                                        playsinline
                                        loop
                                        src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                                </video>
                            </div>
                        <?php endif; ?>

                        <?php
                        $file = CFile::ResizeImageGet(
                            $imageId,
                            ['width' => 760, 'height' => 760],
                            BX_RESIZE_IMAGE_EXACT,
                            true
                        );
                        if (!empty($arResult['PROPERTIES']['VIDEO']['VALUE'])) {
                            $index++;
                        }
                        ?>

                        <div class="swiper-slide  gallery-item">
                            <img
                                    data-index="<?= $index ?>"
                                    src="<?= $file['src'] ?>"
                                    width="<?= $file['width'] ?>"
                                    height="<?= $file['height'] ?>"
                                    alt="Фото"
                                    loading="lazy"
                            >
                        </div>

                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="images">
                <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>
                    <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                        <video
                                class="catalog-cart-video gallery-item"
                                data-index="0"
                                autoplay
                                muted
                                playsinline
                                loop
                                src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                        </video>
                    <?php endif; ?>

                    <?php
                    $file = CFile::GetFileArray($imageId);

                    if (!empty($arResult['PROPERTIES']['VIDEO']['VALUE'])) {
                        $index++;
                    }
                    ?>

                    <img
                            src="<?= $file['SRC'] ?>"
                            width=""
                            height=""
                            alt="Фото"
                            loading="lazy"
                            data-index="<?= $index ?>"
                            class="gallery-item"
                    >

                <?php endforeach; ?>
            </div>
        </div>
        <div class="tovar__right">
            <?php $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "breadcrumb",
                array(
                    "COMPONENT_TEMPLATE" => "breadcrumb",
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "-1"
                ),
                false
            ); ?>
            <div class="tovar__head">
                <h1 class="h2"><?= $arResult['NAME'] ?></h1>

                <span class="favorite-btn favor <?php if ($active): ?>active<?php endif; ?>"
                      data-item="<?= $arResult['ID'] ?>"></span>
            </div>
            <?php if($arResult['OFFERS'][0]['PROPERTIES']['ARTICLE']['VALUE']):?>
            <div class="product-article" id="productArticle" style="margin-bottom: 10px">
                Артикул: <?= $arResult['OFFERS'][0]['PROPERTIES']['ARTICLE']['VALUE'] ?>
            </div>
            <?php endif;?>
            <?php
            $price = $arResult['OFFERS'][0]['ITEM_PRICES'][0];
            ?>

            <div class="price_wrapper">
                <p class="tovar__price" id="productPrice">
                    <?= $price['PRINT_PRICE'] ?>
                </p>

                <p
                        class="tovar__price tovar__price__wishoout__discont"
                        id="productOldPrice"
                        <?php if ($price['PRICE'] >= $price['BASE_PRICE']): ?>
                            style="display:none"
                        <?php endif; ?>
                >
                    <?= $price['PRINT_BASE_PRICE'] ?>
                </p>
            </div>
            <form action="#" class="tovar__form">
                <div class="tovar__color">
                    <div class="tovar__color-text">
                        <p class="tovar__color-title">Цвет</p>
                        <p class="tovar__color-current"><?= $arResult['CURRENT_COLOR'] ?></p>
                    </div>
                    <?php if (!empty($arResult['OTHER_COLORS'])): ?>
                        <div class="tovar__colors">
                            <?php foreach ($arResult['OTHER_COLORS'] as $value): ?>
                                <label class="tovar__color-item">
                                    <a href="<?= $value['LINK'] ?>"
                                       class="tovar__color-circle <?php if ($APPLICATION->GetCurPage() == $value['LINK']): ?>active<?php endif; ?>"
                                       title="<?= $value['ANCHOR'] ?>">
                                        <span style="background: #<?= $value['COLOR'] ?>;"></span>
                                    </a>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="tovar__colors">
                            <label class="tovar__color-item">
                                <span class="tovar__color-circle">
                                    <span style="background: #<?= $arResult['CURRENT_COLOR_XML'] ?>;"></span>
                                </span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $offersData = [];
                foreach ($arResult['OFFERS'] as $index => $offer)
                {
                    $offersData[] = [
                            'ID' => $offer['ID'],
                            'SIZE' => $offer['PROPERTIES']['SIZE']['VALUE'],
                            'ARTICLE' => $offer['PROPERTIES']['ARTICLE']['VALUE'],
                            'PRICE' => $offer['ITEM_PRICES'][0]['PRINT_PRICE'],
                            'BASE_PRICE' => $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'],
                            'PRICE_VALUE' => $offer['ITEM_PRICES'][0]['PRICE'],
                            'BASE_PRICE_VALUE' => $offer['ITEM_PRICES'][0]['BASE_PRICE'],
                    ];
                }
                ?>
                <div class="tovar__size">
                    <div class="tovar__sizes">
                        <div class="title tovar__size-title">
                            Размер
                        </div>
                        <ul class="product-sizes">
                            <?php foreach ($offersData as $key => $offer): ?>

                                <li
                                        class="product-size-item <?= $key === 0 ? 'active' : '' ?>"
                                        data-offer-id="<?= $offer['ID'] ?>"
                                        data-size="<?= $offer['SIZE'] ?>"
                                        data-article="<?= htmlspecialcharsbx($offer['ARTICLE']) ?>"
                                        data-price="<?= htmlspecialcharsbx($offer['PRICE']) ?>"
                                        data-base-price="<?= htmlspecialcharsbx($offer['BASE_PRICE']) ?>"
                                        data-price-value="<?= $offer['PRICE_VALUE'] ?>"
                                        data-base-price-value="<?= $offer['BASE_PRICE_VALUE'] ?>"
                                >
                                    <?= $offer['SIZE'] ?>
                                </li>
                            <?php endforeach; ?>

                        </ul>

                    </div>

                    <a href="#" data-hystmodal="#sizeModal" class="tovar__size-btn">
                        Определить размер
                    </a>

                </div>
                <script>
                    let currentOfferId = <?= (int)$offersData[0]['ID'] ?>;
                </script>

                <?php if ($arResult['PROPERTIES']['AVAILABILITY']['VALUE'] !== 'Нет в наличии'): ?>
                    <a class="black-btn" id="addToBasket"
                       href="javascript:void(0);">
                        <span>Добавить в корзину</span>
                    </a>
                <?php else: ?>
                    <a class="black-btn" style="background-color: grey; cursor: not-allowed">
                        <span>Добавить в корзину</span>
                    </a>
                <?php endif; ?>
            </form>
            <a href="#" class="tovar__link" data-hystmodal="#descriptionModal">Описание</a>
            <a href="#" class="tovar__link" data-hystmodal="#howModal">Состав и уход</a>
            <a href="/customers/?code=delivery" class="tovar__link">Доставка и возврат</a>
        </div>
        <script>
            const offers = <?=CUtil::PhpToJSObject(array_map(function($offer){
                return [
                        'ID' => $offer['ID'],
                        'SIZE_ID' => $offer['TREE']['PROP_15'],
                        'SIZE' => $offer['PROPERTIES']['SIZE']['VALUE'],
                        'ARTICLE' => $offer['PROPERTIES']['ARTICLE']['VALUE'],
                        'PRICE' => $offer['ITEM_PRICES'][0]['PRINT_PRICE'],
                        'BASE_PRICE' => $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'],
                        'PRICE_VALUE' => $offer['ITEM_PRICES'][0]['PRICE'],
                        'BASE_PRICE_VALUE' => $offer['ITEM_PRICES'][0]['BASE_PRICE'],
                ];
            }, $arResult['OFFERS']))?>;
        </script>
    </section>
    <script>
    </script>
<?php /** Конец карточки товара*/ ?>
<?php /** Начало модалки*/ ?>
    <!-- Модалка -->
    <div class="slider-modal" id="sliderModal">
        <button class="slider-close">✕</button>

        <!-- Основной слайдер -->
        <div class="swiper main-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>

                    <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                        <div class="swiper-slide">
                            <div class="swiper-zoom-container">
                                <video
                                        class="catalog-cart-video-modal"
                                        autoplay
                                        muted
                                        playsinline
                                        loop
                                        src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                                </video>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    $file = CFile::GetFileArray($imageId);
                    ?>

                    <div class="swiper-slide">
                        <div class="swiper-zoom-container">
                            <img src="<?= $file['SRC'] ?>" width="" height="" alt="Фото"
                                 loading="lazy">
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <!-- Стрелки -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <!-- Миниатюры -->
        <div class="swiper thumbs-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>
                    <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                        <div class="swiper-slide">
                            <video
                                    class="catalog-cart-video-modal-mini"
                                    muted
                                    playsinline
                                    src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                            </video>
                        </div>
                    <?php endif; ?>
                    <?php
                    $file = CFile::GetFileArray($imageId);
                    ?>
                    <div class="swiper-slide">
                        <img src="<?= $file['SRC'] ?>" width="" height="" alt="Фото" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('sliderModal');
            const closeBtn = modal.querySelector('.slider-close');

            const thumbsSwiper = new Swiper('.thumbs-swiper', {
                slidesPerView: 20,
                spaceBetween: 10,
                watchSlidesProgress: true,
            });

            const mainSwiper = new Swiper('.main-swiper', {
                spaceBetween: 10,
                thumbs: { swiper: thumbsSwiper },
                zoom: { maxRatio: 5 },

                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

                keyboard: {
                    enabled: true,
                    onlyInViewport: false,
                },
            });

            // ✅ Делегирование
            document.addEventListener('click', function (e) {
                //console.log(e)
                const item = e.target.closest('.gallery-item');
                if (!item) return;

                const index = Number(item.dataset.index ?? 0);

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                mainSwiper.update();
                thumbsSwiper.update();
                mainSwiper.slideTo(index, 0);
            });

            closeBtn.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
<?php /** Конец модалки*/ ?>
<!--
    <section class="products products_others">
        <div class="container">
            <p class="h2">Может вам понравиться</p>
        </div>
        <?php
        $currentSectionID = $arResult['IBLOCK_SECTION_ID'];
        global $arrFilter;
        $arrFilter = [];
        $arrFilter = array("!ID" => $arResult['ID']);
        //$arrFilter = array("IBLOCK_SECTION_ID" => $arResult['IBLOCK_SECTION_ID']);
        ?>
        <?php $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "main",
            array(
                "ACTION_VARIABLE" => "action",
                "ADD_PICT_PROP" => "IMAGES",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "ADD_SECTIONS_CHAIN" => "N",
                "ADD_TO_BASKET_ACTION" => "ADD",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "Y",
                "BACKGROUND_IMAGE" => "UF_BACKGROUND_IMAGE",
                "BASKET_URL" => "",
                "BRAND_PROPERTY" => "COLLECTION",
                "BROWSER_TITLE" => "-",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "36000000",
                "CACHE_TYPE" => "A",
                "COMPATIBLE_MODE" => "Y",
                "CONVERT_CURRENCY" => "Y",
                "CURRENCY_ID" => "RUB",
                "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
                "DATA_LAYER_NAME" => "dataLayer",
                "DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
                "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                "DISPLAY_BOTTOM_PAGER" => "Y",
                "DISPLAY_TOP_PAGER" => "N",
//                "SORT_BY1" => "IBLOCK_SECTION_ID",  // первичная сортировка по разделу
//                "SORT_ORDER1" => "DESC",
                "ELEMENT_SORT_FIELD" => "CASE 
                    WHEN IBLOCK_SECTION_ID = {$currentSectionID} THEN 0 
                    ELSE 1 
                END",
                "ELEMENT_SORT_ORDER" => "ASK",
                "ELEMENT_SORT_FIELD2" => "id",
                "ELEMENT_SORT_ORDER2" => "desc",
                "ENLARGE_PRODUCT" => "PROP",
                "ENLARGE_PROP" => "COLLECTION",
                "FILTER_NAME" => "arrFilter",
                "HIDE_NOT_AVAILABLE" => "N",
                "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                "IBLOCK_ID" => "2",
                "IBLOCK_TYPE" => "rest_entity",
                "INCLUDE_SUBSECTIONS" => "Y",
                "LABEL_PROP" => array(),
                "LABEL_PROP_MOBILE" => "",
                "LABEL_PROP_POSITION" => "top-left",
                "LAZY_LOAD" => "Y",
                "LINE_ELEMENT_COUNT" => "3",
                "LOAD_ON_SCROLL" => "N",
                "MESSAGE_404" => "",
                "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_BTN_LAZY_LOAD" => "Показать ещё",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "MESS_NOT_AVAILABLE" => "Нет в наличии",
                "META_DESCRIPTION" => "-",
                "META_KEYWORDS" => "-",
                "OFFERS_CART_PROPERTIES" => array(
                    0 => "ARTNUMBER",
                    1 => "COLOR_REF",
                    2 => "SIZES_SHOES",
                    3 => "SIZES_CLOTHES",
                ),
                "OFFERS_FIELD_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_LIMIT" => "5",
                "OFFERS_PROPERTY_CODE" => array(
                    0 => "COLOR_REF",
                    1 => "SIZES_SHOES",
                    2 => "SIZES_CLOTHES",
                    3 => "",
                ),
                "OFFERS_SORT_FIELD" => "sort",
                "OFFERS_SORT_FIELD2" => "id",
                "OFFERS_SORT_ORDER" => "asc",
                "OFFERS_SORT_ORDER2" => "desc",
                "OFFER_ADD_PICT_PROP" => "",
                "OFFER_TREE_PROPS" => array(
                    0 => "COLOR_REF",
                    1 => "SIZES_SHOES",
                    2 => "SIZES_CLOTHES",
                ),
                "PAGER_BASE_LINK_ENABLE" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_TEMPLATE" => ".default",
                "PAGER_TITLE" => "Товары",
                "PAGE_ELEMENT_COUNT" => "6",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "PRICE_CODE" => array(
                    0 => "BASE",
                ),
                "PRICE_VAT_INCLUDE" => "Y",
                "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
                "PRODUCT_DISPLAY_MODE" => "Y",
                "PRODUCT_ID_VARIABLE" => "id",
                "PRODUCT_PROPERTIES" => array(
                    0 => "NEWPRODUCT",
                    1 => "MATERIAL",
                ),
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PRODUCT_QUANTITY_VARIABLE" => "",
                "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false}]",
                "PRODUCT_SUBSCRIPTION" => "Y",
                "PROPERTY_CODE" => array(
                    0 => "NEWPRODUCT",
                    1 => "",
                ),
                "PROPERTY_CODE_MOBILE" => array(
                    0 => "IMAGES",
                ),
                "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                "RCM_TYPE" => "personal",
                "SECTION_CODE" => $_REQUEST["SECTION_CODE"],
                "SECTION_ID" => "",
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SECTION_URL" => "",
                "SECTION_USER_FIELDS" => array(
                    0 => "",
                    1 => "",
                ),
                "SEF_MODE" => "Y",
                "SET_BROWSER_TITLE" => "Y",
                "SET_LAST_MODIFIED" => "N",
                "SET_META_DESCRIPTION" => "Y",
                "SET_META_KEYWORDS" => "Y",
                "SET_STATUS_404" => "N",
                "SET_TITLE" => "Y",
                "SHOW_404" => "N",
                "SHOW_ALL_WO_SECTION" => "Y",
                "SHOW_CLOSE_POPUP" => "N",
                "SHOW_DISCOUNT_PERCENT" => "N",
                "SHOW_FROM_SECTION" => "N",
                "SHOW_MAX_QUANTITY" => "N",
                "SHOW_OLD_PRICE" => "N",
                "SHOW_PRICE_COUNT" => "1",
                "SHOW_SLIDER" => "Y",
                "SLIDER_INTERVAL" => "3000",
                "SLIDER_PROGRESS" => "N",
                "TEMPLATE_THEME" => "blue",
                "USE_ENHANCED_ECOMMERCE" => "Y",
                "USE_MAIN_ELEMENT_SECTION" => "N",
                "USE_PRICE_COUNT" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "COMPONENT_TEMPLATE" => "main",
                "MESS_NOT_AVAILABLE_SERVICE" => "Недоступно",
                "SEF_RULE" => "#SECTION_CODE#",
                "SECTION_CODE_PATH" => "",
                "DISPLAY_COMPARE" => "N"
            ),
            false
        ); ?>
    </section>
-->
    <div class="hystmodal" id="sizeModal" aria-hidden="true">
        <?php //pr($arResult['SECTION_CODE'])?>
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="sizes">
                    <?php if (!empty($arResult['PROPERTIES']['DETERMINE']['VALUE']['TEXT'])): ?>
                        <?= html_entity_decode($arResult['PROPERTIES']['DETERMINE']['VALUE']['TEXT']) ?>
                    <?php else: ?>
                        <?= $arResult['SECTION']['DESCRIPTION'] ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="howModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="how">
                    <?php if (!empty($arResult['PROPERTIES']['INFO']['VALUE']['TEXT'])): ?>
                        <?= html_entity_decode($arResult['PROPERTIES']['INFO']['VALUE']['TEXT']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="descriptionModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <p class="h2">Информация о товаре</p>
                <div class="gray">
                    <?= $arResult['DETAIL_TEXT'] ?>
                </div>
            </div>
        </div>
    </div>

<div class="hystmodal" id="addBasketModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="thanks" style="flex-direction: column">
                <p class="h2">Товар добавлен в корзину!</p>
                <a href="/basket/">Перейти в корзину</a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="errorBasketModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="thanks">
                <p class="h2">Не удалось добавить товар в корзину</p>
            </div>
        </div>
    </div>
</div>

<div class="hystmodal" id="addFavoriteModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks" style="flex-direction: column">
                <p class="h2">Товар добавлен в Избранное!</p>
                <a href="/profile/favorite/" class="">Всё избранное</a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="delFavoriteModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks">
                <p class="h2">Товар удалён из Избранного!</p>
            </div>
        </div>
    </div>
</div>
<?php
unset($actualItem, $itemIds, $jsParams);
?>

