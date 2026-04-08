<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
$this->setFrameMode(true);
$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "tree", array(
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "SECTION_ID" => "0",
    "COUNT_ELEMENTS" => "Y",
    "TOP_DEPTH" => "2",
    "SECTION_URL" => $arParams["SECTION_URL"],
    "CACHE_TYPE" => 'N',
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "DISPLAY_PANEL" => "N",
    "ADD_SECTIONS_CHAIN" => $arParams['ADD_SECTIONS_CHAIN'],
    "SECTION_USER_FIELDS" => $arParams["SECTION_USER_FIELDS"],
    "CURRENT_SECTION_ID" => $arResult["ID"]
),
    $component
);
$APPLICATION->IncludeComponent("bitrix:catalog.section", "links_catalog", array(
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
    //"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
    "SHOW_ALL_WO_SECTION" => "Y", // Важный параметр для отображения всех товаров
    "PAGE_ELEMENT_COUNT" => 100, // Без пагинации
    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
    "BASKET_URL" => $arParams["BASKET_URL"]
), $component);

$APPLICATION->IncludeComponent("bitrix:catalog.smart.filter", "", array(
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "SECTION_ID" => '',
    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
    "FILTER_NAME" => $arParams["FILTER_NAME"],
    "PRICE_CODE" => $arParams["~PRICE_CODE"],
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
    "SAVE_IN_SESSION" => "N",
    "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
    "XML_EXPORT" => "N",
    "SECTION_TITLE" => "NAME",
    "SECTION_DESCRIPTION" => "DESCRIPTION",
    'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
    "SEF_MODE" => $arParams["SEF_MODE"],
    "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
    "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
    "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
),
    $component,
    array('HIDE_ICONS' => 'Y')
);