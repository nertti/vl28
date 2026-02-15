<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */

foreach ($arResult['SEARCH'] as &$arItem) {
    $arSelect = array("*");
    $arFilter = array("IBLOCK_ID" => 2, "ID" => $arItem['ITEM_ID'], "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties(); // получаем значения свойств

        $price = CCatalogProduct::GetByIDEx(
            $arItem['ITEM_ID'],
        );

        $arItem['DETAIL_PICTURE'] = $arFields['DETAIL_PICTURE'];
        $arItem['IMAGES'] = $arProps['IMAGES'];
        $arItem['PRICE'] = $price['PRICES'][1]['PRICE'];
    }
}