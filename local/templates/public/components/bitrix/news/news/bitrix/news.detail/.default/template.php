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
$this->setFrameMode(true);
?>
<section class="default top0">
    <div class="container ">
        <span class="date"><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></span>
        <?=$arResult['~DETAIL_TEXT']?>
        <?php $APPLICATION->IncludeComponent(
            "sprint.editor:blocks",
            ".default",
            array(
                "ELEMENT_ID" => $arResult["ID"],
                "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                "PROPERTY_CODE" => 'CONTENT',
            ),
            $component,
        ); ?>
    </div>
    <div class="container">
        <a href="/news/" class="default__link default__link_news">Читать другие статьи</a>
    </div>
</section>
