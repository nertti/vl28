<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
    <div class="content__wrap">
    <?php foreach ($arResult["ITEMS"] as $arItem): ?>
        <?php
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
            <?php if ($arItem['PROPERTIES']['TYPE']['VALUE_XML_ID'] === 'video'): ?>
            <div id="<?= $this->GetEditAreaId($arItem['ID']); ?>" class="content__video video-block content__item">
                <video loop="" muted="" defaultmuted="" playsinline="" autoplay="" height="838">
                    <source src="<?= CFile::GetPath($arItem['PROPERTIES']['VIDEO_FILE']['VALUE']); ?>" type="video/mp4">
                </video>
            </div>
            <?php else: ?>
                <img id="<?= $this->GetEditAreaId($arItem['ID']); ?>" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" class="content__item">
            <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>