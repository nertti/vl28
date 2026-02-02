<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
    "bitrix:menu.sections",
    "",
    [
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => 2,                // ID каталога
        "SECTION_URL" => "/catalog/#SECTION_CODE#/",
        "DEPTH_LEVEL" => 1,
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
    ],
    false,
    ["HIDE_ICONS" => "Y"]
);
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);