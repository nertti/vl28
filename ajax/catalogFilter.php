<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
/** @global CMain $APPLICATION */
/** @array  $_POST */

$arrFilter = [];

if (isset($_POST['moto'])){
    $arrFilter['PROPERTY_COLLECTION'][] = 14;
}
if (isset($_POST['everyday'])){
    $arrFilter['PROPERTY_COLLECTION'][] = 13;
}

if (isset($_POST['cotton'])){
    $arrFilter['PROPERTY_MATERIAL'][] = 15;
}
if (isset($_POST['kasemir'])){
    $arrFilter['PROPERTY_MATERIAL'][] = 16;
}

if (isset($_POST['graphit'])){
    $arrFilter["OFFERS"]['PROPERTY_COLOR'][] = 64;
}
if (isset($_POST['grey'])){
    $arrFilter["OFFERS"]['PROPERTY_COLOR'][] = 63;
}
if (isset($_POST['white'])){
    $arrFilter["OFFERS"]['PROPERTY_COLOR'][] = 62;
}
if (isset($_POST['black'])){
    $arrFilter["OFFERS"]['PROPERTY_COLOR'][] = 61;
}
if (isset($_POST['bisque'])){
    $arrFilter["OFFERS"]['PROPERTY_COLOR'][] = 153;
}

if (isset($_POST['xs'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 7;
}
if (isset($_POST['s'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 8;
}
if (isset($_POST['m'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 9;
}
if (isset($_POST['l'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 10;
}
if (isset($_POST['xl'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 11;
}
if (isset($_POST['xxl'])){
    $arrFilter["OFFERS"]['PROPERTY_SIZE'][] = 12;
}



$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "filter",
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
        "BASKET_URL" => "/personal/basket.php",
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
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
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
        "PAGE_ELEMENT_COUNT" => "20",
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
        //"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
        "SECTION_ID" => $_POST["section"],
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
);
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
