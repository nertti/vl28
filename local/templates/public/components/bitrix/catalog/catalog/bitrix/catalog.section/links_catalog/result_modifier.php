<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

foreach ($arResult["ITEMS"] as $cell => &$arElement){
    $arElement['PRICE'] = CPrice::GetBasePrice($arElement['ID']);
}