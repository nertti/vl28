<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);?>
<form action="<?=$arResult["FORM_ACTION"]?>" role="search" method="get" class="search-form">
    <div style="position: relative; display: flex; align-items: center; flex-grow: 1;">
        <input type="search"
               id="searchInput"
               class="search-form__input"
               placeholder="Введите что вы хотите найти?"
               value=""
               name="q"
               autocomplete="off">

        <!-- Кнопка очистки -->
        <button type="reset" id="searchClear" class="search-form__clear">&times;</button>
    </div>
    <input type="submit" class="main-btn" value="Найти">
</form>