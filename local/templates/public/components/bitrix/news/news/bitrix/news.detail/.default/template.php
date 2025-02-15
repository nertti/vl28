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
<div class="container">
<span class="date"><?= $arResult['DISPLAY_ACTIVE_FROM'] ?></span>
<?=$arResult['~DETAIL_TEXT']?>
<a href="<?=$arResult['PROPERTIES']['SOURCE']['~VALUE']?>" class="default__link default__link_small gray"><?=$arResult['PROPERTIES']['SOURCE']['NAME']?></a>
</div>
<?php if (!empty($arResult['PROPERTIES']['PHOTOS']['VALUE'])): ?>
<div class="default__gallery">
    <?php foreach($arResult['PROPERTIES']['PHOTOS']['VALUE'] as $arPhoto):?>
        <img class="default__img default__img_s33" src="<?= CFile::GetPath($arPhoto); ?>" alt="Фото">
    <?php endforeach;?>
</div>
<?php endif;?>
<div class="container">
    <a href="/news/" class="default__link default__link_news">Читать другие статьи</a>
</div>