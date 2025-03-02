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
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
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
    "SHOW_ALL_WO_SECTION" => "Y", // Важный параметр для отображения всех товаров
    "PAGE_ELEMENT_COUNT" => 0, // Без пагинации
    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
    "BASKET_URL" => $arParams["BASKET_URL"]
), $component);