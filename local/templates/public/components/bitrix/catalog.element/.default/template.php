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
//$this->addExternalCss('/bitrix/css/main/bootstrap.css');

$templateLibrary = array('popup', 'fx', 'ui.fonts.opensans');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$haveOffers = !empty($arResult['OFFERS']);

$templateData = [
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'ITEM' => [
        'ID' => $arResult['ID'],
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
    ],
];
if ($haveOffers) {
    $templateData['ITEM']['OFFERS_SELECTED'] = $arResult['OFFERS_SELECTED'];
    $templateData['ITEM']['JS_OFFERS'] = $arResult['JS_OFFERS'];
}
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
    'ID' => $mainId,
    'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
    'STICKER_ID' => $mainId . '_sticker',
    'BIG_SLIDER_ID' => $mainId . '_big_slider',
    'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId . '_slider_cont',
    'OLD_PRICE_ID' => $mainId . '_old_price',
    'PRICE_ID' => $mainId . '_price',
    'DESCRIPTION_ID' => $mainId . '_description',
    'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
    'PRICE_TOTAL' => $mainId . '_price_total',
    'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
    'QUANTITY_ID' => $mainId . '_quantity',
    'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
    'QUANTITY_UP_ID' => $mainId . '_quant_up',
    'QUANTITY_MEASURE' => $mainId . '_quant_measure',
    'QUANTITY_LIMIT' => $mainId . '_quant_limit',
    'BUY_LINK' => $mainId . '_buy_link',
    'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
    'COMPARE_LINK' => $mainId . '_compare_link',
    'TREE_ID' => $mainId . '_skudiv',
    'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
    'OFFER_GROUP' => $mainId . '_set_group_',
    'BASKET_PROP_DIV' => $mainId . '_basket_prop',
    'SUBSCRIBE_LINK' => $mainId . '_subscribe',
    'TABS_ID' => $mainId . '_tabs',
    'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
    'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

if ($haveOffers) {
    $actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
    $showSliderControls = false;

    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['MORE_PHOTO_COUNT'] > 1) {
            $showSliderControls = true;
            break;
        }
    }
} else {
    $actualItem = $arResult;
    $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y') {
    $skuDescription = false;
    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '') {
            $skuDescription = true;
            break;
        }
    }
    $showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
} else {
    $showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}

$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-default' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE) {
    $arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
    $arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');

    $arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
    $arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE');
} else {
    $arParams['~MESS_NOT_AVAILABLE'] ??= '';
    $arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');

    $arParams['MESS_NOT_AVAILABLE'] ??= '';
    $arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
        ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
    'left' => 'product-item-label-left',
    'center' => 'product-item-label-center',
    'right' => 'product-item-label-right',
    'bottom' => 'product-item-label-bottom',
    'middle' => 'product-item-label-middle',
    'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION'])) {
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}
?>
<?php
//pr($arResult);
?>

<?php
global $APPLICATION;

if (!$USER->IsAuthorized()) {
    $arFavorites = unserialize($APPLICATION->get_cookie("favorites"));
    //pr($arFavorites);
} else {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arFavorites = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя

}
$active = false;
foreach ($arFavorites as $favorite) {
    if ($favorite == $arResult['ID']) {
        $active = true;
    }
}
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
                    <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $image): ?>
                        <div class="swiper-slide">
                            <img src="<?= CFile::getPath($image) ?>" alt="Фото">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
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
            <p class="tovar__price"><?= $arResult['JS_OFFERS'][0]['ITEM_PRICES'][0]['PRINT_PRICE'] ?></p>
            <form action="#" class="tovar__form">
                <?php if (isset($arResult['SKU_PROPS'])): ?>
                    <?php if ($haveOffers && !empty($arResult['OFFERS_PROP'])): ?>
                        <?php foreach ($arResult['SKU_PROPS'] as $key => $skuProperty): ?>
                            <?php
                            if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
                                continue;

                            $propertyId = $skuProperty['ID'];
                            $skuProps[] = array(
                                'ID' => $propertyId,
                                'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                'VALUES' => $skuProperty['VALUES'],
                                'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                            );
                            ?>
                            <?php if ($key == 'COLOR' && false): ?>
                                <?php
                                pr($skuProperty);
                                // на этапе разработки
                                ?>
                                <div class="tovar__color">
                                    <div class="tovar__color-text">
                                        <p class="tovar__color-title">Цвет</p>
                                        <p class="tovar__color-current">Графит</p>
                                    </div>
                                    <div class="tovar__colors">
                                        <?php foreach ($skuProperty['VALUES'] as $value): ?>
                                            <label class="tovar__color-item">
                                                <input type="radio" value="Черный" name="color" checked="">
                                                <span class="tovar__color-circle">
                                                  <span style="background: #000;"></span>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($key == 'SIZE'): ?>
                                <div class="tovar__size">
                                    <div class="tovar__sizes">
                                        <div class="product-item-detail-info-section">
                                            <div id="<?= $itemIds['TREE_ID'] ?>">
                                                <div class="product-item-detail-info-container"
                                                     data-entity="sku-line-block">
                                                    <div class="title tovar__size-title"><?= htmlspecialcharsEx($skuProperty['NAME']) ?></div>
                                                    <div class="product-item-scu-container">
                                                        <div class="product-item-scu-block">
                                                            <div class="product-item-scu-list">
                                                                <ul class="product-item-scu-item-list">
                                                                    <?php
                                                                    foreach ($skuProperty['VALUES'] as &$value) {
                                                                        $value['NAME'] = htmlspecialcharsbx($value['NAME']);
                                                                        ?>
                                                                        <li class="product-item-scu-item-text-container"
                                                                            title="<?= $value['NAME'] ?>"
                                                                            data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                            data-onevalue="<?= $value['ID'] ?>">
                                                                            <div class="product-item-scu-item-text-block">
                                                                                <div class="product-item-scu-item-text"><?= $value['NAME'] ?></div>
                                                                            </div>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                                <div style="clear: both;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="#" data-hystmodal="#sizeModal" class="tovar__size-btn">Определить
                                        размер</a>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <a class="black-btn <?= $buyButtonClassName ?>" id="<?= $itemIds['BUY_LINK'] ?>"
                   href="javascript:void(0);">
                    <span>Добавить в корзину</span>
                </a>
            </form>
            <a href="#" class="tovar__link" data-hystmodal="#descriptionModal">Описание</a>
            <a href="#" class="tovar__link" data-hystmodal="#howModal">Состав и уход</a>
            <a href="http://vl26908655.nichost.ru/customers/?code=delivery" class="tovar__link">Доставка и возврат</a>
        </div>
    </section>
<?php /** Конец карточки товара*/ ?>
    <section class="products products_others">
        <div class="container">
            <p class="h2">Вам может понравится</p>
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
    <div class="bx-catalog-element bx-<?= $arParams['TEMPLATE_THEME'] ?>" id="<?= $itemIds['ID'] ?>"
         itemscope itemtype="http://schema.org/Product"
         style="display: none">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="product-item-detail-slider-container" id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
                        <span class="product-item-detail-slider-close" data-entity="close-popup"></span>
                        <div class="product-item-detail-slider-block
						<?= ($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '') ?>"
                             data-entity="images-slider-block">
                            <span class="product-item-detail-slider-left" data-entity="slider-control-left"
                                  style="display: none;"></span>
                            <span class="product-item-detail-slider-right" data-entity="slider-control-right"
                                  style="display: none;"></span>
                            <div class="product-item-label-text <?= $labelPositionClass ?>"
                                 id="<?= $itemIds['STICKER_ID'] ?>"
                                <?= (!$arResult['LABEL'] ? 'style="display: none;"' : '') ?>>
                                <?php
                                if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE'])) {
                                    foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value) {
                                        ?>
                                        <div<?= (!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '') ?>>
                                            <span title="<?= $value ?>"><?= $value ?></span>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <?php
                            if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y') {
                                if ($haveOffers) {
                                    ?>
                                    <div class="product-item-label-ring <?= $discountPositionClass ?>"
                                         id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"
                                         style="display: none;">
                                    </div>
                                    <?php
                                } else {
                                    if ($price['DISCOUNT'] > 0) {
                                        ?>
                                        <div class="product-item-label-ring <?= $discountPositionClass ?>"
                                             id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"
                                             title="<?= -$price['PERCENT'] ?>%">
                                            <span><?= -$price['PERCENT'] ?>%</span>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            <div class="product-item-detail-slider-images-container" data-entity="images-container">
                                <?php
                                if (!empty($actualItem['MORE_PHOTO'])) {
                                    foreach ($actualItem['MORE_PHOTO'] as $key => $photo) {
                                        ?>
                                        <div class="product-item-detail-slider-image<?= ($key == 0 ? ' active' : '') ?>"
                                             data-entity="image" data-id="<?= $photo['ID'] ?>">
                                            <img src="<?= $photo['SRC'] ?>" alt="<?= $alt ?>"
                                                 title="<?= $title ?>"<?= ($key == 0 ? ' itemprop="image"' : '') ?>>
                                        </div>
                                        <?php
                                    }
                                }

                                if ($arParams['SLIDER_PROGRESS'] === 'Y') {
                                    ?>
                                    <div class="product-item-detail-slider-progress-bar"
                                         data-entity="slider-progress-bar" style="width: 0;"></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        if ($showSliderControls) {
                            if ($haveOffers) {
                                foreach ($arResult['OFFERS'] as $keyOffer => $offer) {
                                    if (!isset($offer['MORE_PHOTO_COUNT']) || $offer['MORE_PHOTO_COUNT'] <= 0)
                                        continue;

                                    $strVisible = $arResult['OFFERS_SELECTED'] == $keyOffer ? '' : 'none';
                                    ?>
                                    <div class="product-item-detail-slider-controls-block"
                                         id="<?= $itemIds['SLIDER_CONT_OF_ID'] . $offer['ID'] ?>"
                                         style="display: <?= $strVisible ?>;">
                                        <?php
                                        foreach ($offer['MORE_PHOTO'] as $keyPhoto => $photo) {
                                            ?>
                                            <div class="product-item-detail-slider-controls-image<?= ($keyPhoto == 0 ? ' active' : '') ?>"
                                                 data-entity="slider-control"
                                                 data-value="<?= $offer['ID'] . '_' . $photo['ID'] ?>">
                                                <img src="<?= $photo['SRC'] ?>">
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="product-item-detail-slider-controls-block"
                                     id="<?= $itemIds['SLIDER_CONT_ID'] ?>">
                                    <?php
                                    if (!empty($actualItem['MORE_PHOTO'])) {
                                        foreach ($actualItem['MORE_PHOTO'] as $key => $photo) {
                                            ?>
                                            <div class="product-item-detail-slider-controls-image<?= ($key == 0 ? ' active' : '') ?>"
                                                 data-entity="slider-control" data-value="<?= $photo['ID'] ?>">
                                                <img src="<?= $photo['SRC'] ?>">
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="product-item-detail-info-section">
                                <?php
                                foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName) {
                                    switch ($blockName) {
                                        case 'sku':
                                            if ($haveOffers && !empty($arResult['OFFERS_PROP'])) {
                                                ?>
                                                <div id="<?= $itemIds['TREE_ID'] ?>">
                                                    <?php
                                                    foreach ($arResult['SKU_PROPS'] as $skuProperty) {
                                                        if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
                                                            continue;

                                                        $propertyId = $skuProperty['ID'];
                                                        $skuProps[] = array(
                                                            'ID' => $propertyId,
                                                            'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                                            'VALUES' => $skuProperty['VALUES'],
                                                            'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                                                        );
                                                        ?>
                                                        <div class="product-item-detail-info-container"
                                                             data-entity="sku-line-block">
                                                            <div class="product-item-detail-info-container-title"><?= htmlspecialcharsEx($skuProperty['NAME']) ?></div>
                                                            <div class="product-item-scu-container">
                                                                <div class="product-item-scu-block">
                                                                    <div class="product-item-scu-list">
                                                                        <ul class="product-item-scu-item-list">
                                                                            <?php
                                                                            foreach ($skuProperty['VALUES'] as &$value) {
                                                                                $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                                                                if ($skuProperty['SHOW_MODE'] === 'PICT') {
                                                                                    ?>
                                                                                    <li class="product-item-scu-item-color-container"
                                                                                        title="<?= $value['NAME'] ?>"
                                                                                        data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                                        data-onevalue="<?= $value['ID'] ?>">
                                                                                        <div class="product-item-scu-item-color-block">
                                                                                            <div class="product-item-scu-item-color"
                                                                                                 title="<?= $value['NAME'] ?>"
                                                                                                 style="background-image: url('<?= $value['PICT']['SRC'] ?>');">
                                                                                            </div>
                                                                                        </div>
                                                                                    </li>
                                                                                    <?php
                                                                                } else {
                                                                                    ?>
                                                                                    <li class="product-item-scu-item-text-container"
                                                                                        title="<?= $value['NAME'] ?>"
                                                                                        data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                                        data-onevalue="<?= $value['ID'] ?>">
                                                                                        <div class="product-item-scu-item-text-block">
                                                                                            <div class="product-item-scu-item-text"><?= $value['NAME'] ?></div>
                                                                                        </div>
                                                                                    </li>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </ul>
                                                                        <div style="clear: both;"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'props':
                                            if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?php
                                                    if (!empty($arResult['DISPLAY_PROPERTIES'])) {
                                                        ?>
                                                        <dl class="product-item-detail-properties">
                                                            <?php
                                                            foreach ($arResult['DISPLAY_PROPERTIES'] as $property) {
                                                                if (isset($arParams['MAIN_BLOCK_PROPERTY_CODE'][$property['CODE']])) {
                                                                    ?>
                                                                    <dt><?= $property['NAME'] ?></dt>
                                                                    <dd><?= (is_array($property['DISPLAY_VALUE'])
                                                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                                                            : $property['DISPLAY_VALUE']) ?>
                                                                    </dd>
                                                                    <?php
                                                                }
                                                            }
                                                            unset($property);
                                                            ?>
                                                        </dl>
                                                        <?php
                                                    }

                                                    if ($arResult['SHOW_OFFERS_PROPS']) {
                                                        ?>
                                                        <dl class="product-item-detail-properties"
                                                            id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>"></dl>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="product-item-detail-pay-block">
                                <?php
                                foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName) {
                                    switch ($blockName) {
                                        case 'rating':
                                            if ($arParams['USE_VOTE_RATING'] === 'Y') {
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <?php
                                                    $APPLICATION->IncludeComponent(
                                                        'bitrix:iblock.vote',
                                                        'stars',
                                                        array(
                                                            'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                                            'ELEMENT_ID' => $arResult['ID'],
                                                            'ELEMENT_CODE' => '',
                                                            'MAX_VOTE' => '5',
                                                            'VOTE_NAMES' => array('1', '2', '3', '4', '5'),
                                                            'SET_STATUS_404' => 'N',
                                                            'DISPLAY_AS_RATING' => $arParams['VOTE_DISPLAY_AS_RATING'],
                                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                            'CACHE_TIME' => $arParams['CACHE_TIME']
                                                        ),
                                                        $component,
                                                        array('HIDE_ICONS' => 'Y')
                                                    );
                                                    ?>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'price':
                                            ?>
                                            <div class="product-item-detail-info-container">
                                                <?php
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y') {
                                                    ?>
                                                    <div class="product-item-detail-price-old"
                                                         id="<?= $itemIds['OLD_PRICE_ID'] ?>"
                                                         style="display: <?= ($showDiscount ? '' : 'none') ?>;">
                                                        <?= ($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '') ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="product-item-detail-price-current"
                                                     id="<?= $itemIds['PRICE_ID'] ?>">
                                                    <?= $price['PRINT_RATIO_PRICE'] ?>
                                                </div>
                                                <?php
                                                if ($arParams['SHOW_OLD_PRICE'] === 'Y') {
                                                    ?>
                                                    <div class="item_economy_price"
                                                         id="<?= $itemIds['DISCOUNT_PRICE_ID'] ?>"
                                                         style="display: <?= ($showDiscount ? '' : 'none') ?>;">
                                                        <?php
                                                        if ($showDiscount) {
                                                            echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            break;

                                        case 'priceRanges':
                                            if ($arParams['USE_PRICE_COUNT']) {
                                                $showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
                                                $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
                                                ?>
                                                <div class="product-item-detail-info-container"
                                                    <?= $showRanges ? '' : 'style="display: none;"' ?>
                                                     data-entity="price-ranges-block">
                                                    <div class="product-item-detail-info-container-title">
                                                        <?= $arParams['MESS_PRICE_RANGES_TITLE'] ?>
                                                        <span data-entity="price-ranges-ratio-header">
														(<?= (Loc::getMessage(
                                                                'CT_BCE_CATALOG_RATIO_PRICE',
                                                                array('#RATIO#' => ($useRatio ? $measureRatio : '1') . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
                                                            )) ?>)
													</span>
                                                    </div>
                                                    <dl class="product-item-detail-properties"
                                                        data-entity="price-ranges-body">
                                                        <?php
                                                        if ($showRanges) {
                                                            foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range) {
                                                                if ($range['HASH'] !== 'ZERO-INF') {
                                                                    $itemPrice = false;

                                                                    foreach ($arResult['ITEM_PRICES'] as $itemPrice) {
                                                                        if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
                                                                            break;
                                                                        }
                                                                    }

                                                                    if ($itemPrice) {
                                                                        ?>
                                                                        <dt>
                                                                            <?php
                                                                            echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_FROM',
                                                                                    array('#FROM#' => $range['SORT_FROM'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
                                                                                ) . ' ';

                                                                            if (is_infinite($range['SORT_TO'])) {
                                                                                echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                                                                            } else {
                                                                                echo Loc::getMessage(
                                                                                    'CT_BCE_CATALOG_RANGE_TO',
                                                                                    array('#TO#' => $range['SORT_TO'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'])
                                                                                );
                                                                            }
                                                                            ?>
                                                                        </dt>
                                                                        <dd><?= ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) ?></dd>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </dl>
                                                </div>
                                                <?php
                                                unset($showRanges, $useRatio, $itemPrice, $range);
                                            }

                                            break;

                                        case 'quantityLimit':
                                            if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                                                if ($haveOffers) {
                                                    ?>
                                                    <div class="product-item-detail-info-container"
                                                         id="<?= $itemIds['QUANTITY_LIMIT'] ?>" style="display: none;">
                                                        <div class="product-item-detail-info-container-title">
                                                            <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:
                                                            <span class="product-item-quantity"
                                                                  data-entity="quantity-limit-value"></span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                } else {
                                                    if (
                                                        $measureRatio
                                                        && (float)$actualItem['PRODUCT']['QUANTITY'] > 0
                                                        && $actualItem['CHECK_QUANTITY']
                                                    ) {
                                                        ?>
                                                        <div class="product-item-detail-info-container"
                                                             id="<?= $itemIds['QUANTITY_LIMIT'] ?>">
                                                            <div class="product-item-detail-info-container-title">
                                                                <?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>:
                                                                <span class="product-item-quantity"
                                                                      data-entity="quantity-limit-value">
																<?php
                                                                if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                                    if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
                                                                        echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                                                                    } else {
                                                                        echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                                                    }
                                                                } else {
                                                                    echo $actualItem['PRODUCT']['QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
                                                                }
                                                                ?>
															</span>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }

                                            break;

                                        case 'quantity':
                                            if ($arParams['USE_PRODUCT_QUANTITY']) {
                                                ?>
                                                <div class="product-item-detail-info-container"
                                                     style="<?= (!$actualItem['CAN_BUY'] ? 'display: none;' : '') ?>"
                                                     data-entity="quantity-block">
                                                    <div class="product-item-detail-info-container-title"><?= Loc::getMessage('CATALOG_QUANTITY') ?></div>
                                                    <div class="product-item-amount">
                                                        <div class="product-item-amount-field-container">
                                                            <span class="product-item-amount-field-btn-minus no-select"
                                                                  id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>"></span>
                                                            <input class="product-item-amount-field"
                                                                   id="<?= $itemIds['QUANTITY_ID'] ?>" type="number"
                                                                   value="<?= $price['MIN_QUANTITY'] ?>">
                                                            <span class="product-item-amount-field-btn-plus no-select"
                                                                  id="<?= $itemIds['QUANTITY_UP_ID'] ?>"></span>
                                                            <span class="product-item-amount-description-container">
															<span id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
																<?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
															</span>
															<span id="<?= $itemIds['PRICE_TOTAL'] ?>"></span>
														</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                            break;

                                        case 'buttons':
                                            ?>
                                            <div data-entity="main-button-container">
                                                <div id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>"
                                                     style="display: <?= ($actualItem['CAN_BUY'] ? '' : 'none') ?>;">
                                                    <?php
                                                    if ($showAddBtn) {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?= $showButtonClassName ?> product-item-detail-buy-button"
                                                               id="<?= $itemIds['ADD_BASKET_LINK'] ?>"
                                                               href="javascript:void(0);">
                                                                <span><?= $arParams['MESS_BTN_ADD_TO_BASKET'] ?></span>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }

                                                    if ($showBuyBtn) {
                                                        ?>
                                                        <div class="product-item-detail-info-container">
                                                            <a class="btn <?= $buyButtonClassName ?> product-item-detail-buy-button"
                                                               id="<?= $itemIds['BUY_LINK'] ?>"
                                                               href="javascript:void(0);">
                                                                <span><?= $arParams['MESS_BTN_BUY'] ?></span>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                                if ($showSubscribe) {
                                                    ?>
                                                    <div class="product-item-detail-info-container">
                                                        <?php
                                                        $APPLICATION->IncludeComponent(
                                                            'bitrix:catalog.product.subscribe',
                                                            '',
                                                            array(
                                                                'CUSTOM_SITE_ID' => $arParams['CUSTOM_SITE_ID'] ?? null,
                                                                'PRODUCT_ID' => $arResult['ID'],
                                                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                                                'BUTTON_CLASS' => 'btn btn-default product-item-detail-buy-button',
                                                                'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                                            ),
                                                            $component,
                                                            array('HIDE_ICONS' => 'Y')
                                                        );
                                                        ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <div class="product-item-detail-info-container">
                                                    <a class="btn btn-link product-item-detail-buy-button"
                                                       id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"
                                                       href="javascript:void(0)"
                                                       rel="nofollow"
                                                       style="display: <?= (!$actualItem['CAN_BUY'] ? '' : 'none') ?>;">
                                                        <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php
                                            break;
                                    }
                                }

                                if ($arParams['DISPLAY_COMPARE']) {
                                    ?>
                                    <div class="product-item-detail-compare-container">
                                        <div class="product-item-detail-compare">
                                            <div class="checkbox">
                                                <label id="<?= $itemIds['COMPARE_LINK'] ?>">
                                                    <input type="checkbox" data-entity="compare-checkbox">
                                                    <span data-entity="compare-title"><?= $arParams['MESS_BTN_COMPARE'] ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($haveOffers) {
            foreach ($arResult['JS_OFFERS'] as $offer) {
                $currentOffersList = array();

                if (!empty($offer['TREE']) && is_array($offer['TREE'])) {
                    foreach ($offer['TREE'] as $propName => $skuId) {
                        $propId = (int)mb_substr($propName, 5);

                        foreach ($skuProps as $prop) {
                            if ($prop['ID'] == $propId) {
                                foreach ($prop['VALUES'] as $propId => $propValue) {
                                    if ($propId == $skuId) {
                                        $currentOffersList[] = $propValue['NAME'];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                $offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
                ?>
                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="sku" content="<?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?>"/>
				<meta itemprop="price" content="<?= $offerPrice['RATIO_PRICE'] ?>"/>
				<meta itemprop="priceCurrency" content="<?= $offerPrice['CURRENCY'] ?>"/>
				<link itemprop="availability"
                      href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
			</span>
                <?php
            }

            unset($offerPrice, $currentOffersList);
        } else {
            ?>
            <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price" content="<?= $price['RATIO_PRICE'] ?>"/>
			<meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>"/>
			<link itemprop="availability"
                  href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
		</span>
            <?php
        }
        ?>
    </div>
    <div class="hystmodal" id="sizeModal" aria-hidden="true">
        <?php //pr($arResult['SECTION_CODE'])?>
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="sizes">
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 't-shirts'): ?>
                        <div class="sizes__title">
                            <p class="h2">Футболки</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>68 см</td>
                                <td>70 см</td>
                                <td>71 см</td>
                            </tr>
                            <tr>
                                <td>Ширина изделия</td>
                                <td>56 см</td>
                                <td>58 см</td>
                                <td>60 см</td>
                            </tr>
                            <tr>
                                <td>Длина плеча</td>
                                <td>15.5 см</td>
                                <td>16 см</td>
                                <td>16.5 см</td>
                            </tr>
                            <tr>
                                <td>Длина рукава</td>
                                <td>26 см</td>
                                <td>26 см</td>
                                <td>26 см</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'hoodies'): ?>
                        <div class="sizes__title">
                            <p class="h2">Худи</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>65 см</td>
                                <td>68 см</td>
                                <td>70 см</td>
                            </tr>
                            <tr>
                                <td>Ширина изделия</td>
                                <td>56 см</td>
                                <td>58 см</td>
                                <td>60 см</td>
                            </tr>
                            <tr>
                                <td>Длина рукова</td>
                                <td>60 см</td>
                                <td>62 см</td>
                                <td>64 см</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'hoodies-with-zipper'): ?>
                        <div class="sizes__title">
                            <p class="h2">
                                Худи на молнии
                            </p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>66 см</td>
                                <td>68 см</td>
                                <td>70 см</td>
                            </tr>
                            <tr>
                                <td>Ширина изделия</td>
                                <td>56 см</td>
                                <td>58 см</td>
                                <td>60 см</td>
                            </tr>
                            <tr>
                                <td>Длина рукава</td>
                                <td>60 см</td>
                                <td>62 см</td>
                                <td>64 см</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'joggers'): ?>
                        <div class="sizes__title">
                            <p class="h2">Джогеры</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват талии</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват бедер</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'sweatshirts'): ?>
                        <div class="sizes__title">
                            <p class="h2">Свитшот</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>65 см</td>
                                <td>68 см</td>
                                <td>70 см</td>
                            </tr>
                            <tr>
                                <td>Ширина изделия</td>
                                <td>56 см</td>
                                <td>58 см</td>
                                <td>60 см</td>
                            </tr>
                            <tr>
                                <td>Длина рукава</td>
                                <td>60 см</td>
                                <td>62 см</td>
                                <td>64 см</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'thermal-underwear'): ?>
                        <div class="sizes__title">
                            <p class="h2">Гоночное термобелье слитное</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>XS/S</th>
                                <th>M</th>
                                <th>L/XL</th>
                            </tr>
                            <tr>
                                <td>Рост</td>
                                <td>175 см</td>
                                <td>185 см</td>
                                <td>190 см</td>
                            </tr>
                            <tr>
                                <td>Обхват груди</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват талии</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват бедер</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Длина рукава</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="sizes__title top40">
                            <p class="h2">
                                Гоночное термобелье раздельное
                            </p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>XS/S</th>
                                <th>M</th>
                                <th>L/XL</th>
                            </tr>
                            <tr>
                                <td>Рост</td>
                                <td>175 см</td>
                                <td>185 см</td>
                                <td>190 см</td>
                            </tr>
                            <tr>
                                <td>Обхват груди</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват талии</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Обхват бедер</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            <tr>
                                <td>Длина рукава</td>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ($arResult['ORIGINAL_PARAMETERS']['SECTION_CODE'] == 'shorts'): ?>
                        <div class="sizes__title">
                            <p class="h2">Шорты</p>
                        </div>
                        <table class="sizes__table">
                            <tbody>
                            <tr>
                                <th>Размеры</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                            </tr>
                            <tr>
                                <td>Длина изделия</td>
                                <td>55 см</td>
                                <td>56 см</td>
                                <td>58 см</td>
                            </tr>
                            <tr>
                                <td>Ширина низа</td>
                                <td>30 см</td>
                                <td>31 см</td>
                                <td>32 см</td>
                            </tr>
                            <tr>
                                <td>Высота пояса</td>
                                <td>6 см</td>
                                <td>6 см</td>
                                <td>6 см</td>
                            </tr>
                            </tbody>
                        </table>
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
                    <p class="h2">Информация об уходе для термобелья</p>
                    <p class="gray">При уходе за термобельем придерживайтесь этих правил:</p>
                    <ol class="gray">
                        <li>
                            В целях гигиены, мы рекомендуем постирать новую вещь перед ноской, так как термобелье
                            используется в качестве первого слоя к телу.
                        </li>
                        <li>
                            Перед стиркой застегните молнию и выверните вещь наизнанку.
                        </li>
                        <li>
                            Стирка при температуре не выше 30 градусов на деликатном режиме, отжим самый минимальный,
                            без скручивания. Лучше поставить функцию «без отжима».
                        </li>
                        <li>
                            Мы рекомендуем использовать стиральные средства для деликатных тканей. Лучше всего подойдут
                            жидкие гели, так как стиральные порошки могут попасть в структуру плетения и плохо
                            выстираться, что скажется на эффективности термобелья.
                        </li>
                        <li>
                            Сушить термобелье&nbsp;необходимо в хорошо проветриваемом помещении, вдали от солнечных
                            лучей и отопительных приборов.&nbsp;
                        </li>
                        <li>
                            Сушка в стиральной машине запрещена
                        </li>
                        <li>
                            Утюжить и отпаривать термобелье не требуется. Любое температурное воздействие болье 30
                            градусов С, для термобелья- запрещено.
                        </li>
                        <li>
                            Храните термобелье&nbsp;в сложенном виде, чтобы избежать деформации.
                        </li>
                    </ol>
                    <p>
            <span class="gray">
              При бережном уходе ваши любимые вещи прослужат не один сезон.
            </span>
                        Мы рекомендуем относиться аккуратно с липучками и другими элементами экипировки, которые могут
                        оставлять зацепки на термобелье.
                    </p>
                    <div class="how__icons">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how1.svg" alt="icon">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how2.svg" alt="icon">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how3.svg" alt="icon">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how4.svg" alt="icon">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how5.svg" alt="icon">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/how6.svg" alt="icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="descriptionModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="gray">
                    <p class="h2">Информация о товаре</p>
                    <?= $arResult['DETAIL_TEXT'] ?>
                </div>
            </div>
        </div>
    </div>


<?php

if ($haveOffers) {
    $offerIds = array();
    $offerCodes = array();

    $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

    foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer) {
        $offerIds[] = (int)$jsOffer['ID'];
        $offerCodes[] = $jsOffer['CODE'];

        $fullOffer = $arResult['OFFERS'][$ind];
        $measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

        $strAllProps = '';
        $strMainProps = '';
        $strPriceRangesRatio = '';
        $strPriceRanges = '';

        if ($arResult['SHOW_OFFERS_PROPS']) {
            if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
                foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property) {
                    $current = '<dt>' . $property['NAME'] . '</dt><dd>' . (
                        is_array($property['VALUE'])
                            ? implode(' / ', $property['VALUE'])
                            : $property['VALUE']
                        ) . '</dd>';
                    $strAllProps .= $current;

                    if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']])) {
                        $strMainProps .= $current;
                    }
                }

                unset($current);
            }
        }

        if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1) {
            $strPriceRangesRatio = '(' . Loc::getMessage(
                    'CT_BCE_CATALOG_RATIO_PRICE',
                    array('#RATIO#' => ($useRatio
                            ? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
                            : '1'
                        ) . ' ' . $measureName)
                ) . ')';

            foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range) {
                if ($range['HASH'] !== 'ZERO-INF') {
                    $itemPrice = false;

                    foreach ($jsOffer['ITEM_PRICES'] as $itemPrice) {
                        if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
                            break;
                        }
                    }

                    if ($itemPrice) {
                        $strPriceRanges .= '<dt>' . Loc::getMessage(
                                'CT_BCE_CATALOG_RANGE_FROM',
                                array('#FROM#' => $range['SORT_FROM'] . ' ' . $measureName)
                            ) . ' ';

                        if (is_infinite($range['SORT_TO'])) {
                            $strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                        } else {
                            $strPriceRanges .= Loc::getMessage(
                                'CT_BCE_CATALOG_RANGE_TO',
                                array('#TO#' => $range['SORT_TO'] . ' ' . $measureName)
                            );
                        }

                        $strPriceRanges .= '</dt><dd>' . ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) . '</dd>';
                    }
                }
            }

            unset($range, $itemPrice);
        }

        $jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
        $jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
        $jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
        $jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
    }

    $templateData['OFFER_IDS'] = $offerIds;
    $templateData['OFFER_CODES'] = $offerCodes;
    unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

    $jsParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => true,
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
            'OFFER_GROUP' => $arResult['OFFER_GROUP'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribe,
            'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
            'ALT' => $alt,
            'TITLE' => $title,
            'MAGNIFIER_ZOOM_PERCENT' => 200,
            'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
            'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
            'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                : null,
            'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'],
            'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE']
        ),
        'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
        'VISUAL' => $itemIds,
        'DEFAULT_PICTURE' => array(
            'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
            'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
        ),
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'ACTIVE' => $arResult['ACTIVE'],
            'NAME' => $arResult['~NAME'],
            'CATEGORY' => $arResult['CATEGORY_PATH'],
            'DETAIL_TEXT' => $arResult['DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'],
            'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'],
            'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE']
        ),
        'BASKET' => array(
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        ),
        'OFFERS' => $arResult['JS_OFFERS'],
        'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
        'TREE_PROPS' => $skuProps
    );
} else {
    $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
    if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties) {
        ?>
        <div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
            <?php
            if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
                foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) {
                    ?>
                    <input type="hidden" name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                           value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
                    <?php
                    unset($arResult['PRODUCT_PROPERTIES'][$propId]);
                }
            }

            $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
            if (!$emptyProductProperties) {
                ?>
                <table>
                    <?php
                    foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo) {
                        ?>
                        <tr>
                            <td><?= $arResult['PROPERTIES'][$propId]['NAME'] ?></td>
                            <td>
                                <?php
                                if (
                                    $arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
                                    && $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
                                ) {
                                    foreach ($propInfo['VALUES'] as $valueId => $value) {
                                        ?>
                                        <label>
                                            <input type="radio"
                                                   name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                                   value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'checked' : '') ?>>
                                            <?= $value ?>
                                        </label>
                                        <br>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]">
                                        <?php
                                        foreach ($propInfo['VALUES'] as $valueId => $value) {
                                            ?>
                                            <option value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? 'selected' : '') ?>>
                                                <?= $value ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            }
            ?>
        </div>
        <?php
    }

    $jsParams = array(
        'CONFIG' => array(
            'USE_CATALOG' => $arResult['CATALOG'],
            'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
            'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
            'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
            'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
            'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
            'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
            'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
            'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
            'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
            'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
            'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
            'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
            'USE_STICKERS' => true,
            'USE_SUBSCRIBE' => $showSubscribe,
            'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
            'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
            'ALT' => $alt,
            'TITLE' => $title,
            'MAGNIFIER_ZOOM_PERCENT' => 200,
            'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
            'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
            'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                : null
        ),
        'VISUAL' => $itemIds,
        'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
        'PRODUCT' => array(
            'ID' => $arResult['ID'],
            'ACTIVE' => $arResult['ACTIVE'],
            'PICT' => reset($arResult['MORE_PHOTO']),
            'NAME' => $arResult['~NAME'],
            'SUBSCRIPTION' => true,
            'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
            'ITEM_PRICES' => $arResult['ITEM_PRICES'],
            'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
            'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
            'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
            'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
            'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
            'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
            'SLIDER' => $arResult['MORE_PHOTO'],
            'CAN_BUY' => $arResult['CAN_BUY'],
            'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
            'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
            'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
            'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
            'CATEGORY' => $arResult['CATEGORY_PATH']
        ),
        'BASKET' => array(
            'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
            'EMPTY_PROPS' => $emptyProductProperties,
            'BASKET_URL' => $arParams['BASKET_URL'],
            'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
            'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
        )
    );
    unset($emptyProductProperties);
}

?>
    <script>
        BX.message({
            ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
            TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
            TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
            BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
            BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
            BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
            BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
            BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
            TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
            COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
            COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
            COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
            PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
            PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
            RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
            RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
            SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
        });

        var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
    </script>
<?php
unset($actualItem, $itemIds, $jsParams);