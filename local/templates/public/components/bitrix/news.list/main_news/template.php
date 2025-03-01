<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

<?php if (!empty($arResult["ITEMS"])): ?>
<p class="h2">Новости</p>
<div class="blog__list blog__list_home">
    <?php foreach($arResult["ITEMS"] as $arItem):?>
        <?php
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?=$arItem['DETAIL_PAGE_URL']?>" class="article">
            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" class="article__img">
            <div class="article__inner">
                <p class="article__title"><?=$arItem['NAME']?></p>
                <p class="article__desc">
                    <?=$arItem['~PREVIEW_TEXT']?>
                </p>
                <span class="article__date"><?=$arItem['ACTIVE_FROM']?></span>
            </div>
        </a>
    <?php endforeach;?>
</div>
<a href="/news/" class="blog__link link">Все новости</a>
<?php endif; ?>
