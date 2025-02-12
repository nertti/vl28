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
    <?php foreach ($arResult["ITEMS"] as $arItem): ?>
        <?php
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <section id="<?= $this->GetEditAreaId($arItem['ID']); ?>" class="banner">
            <?php if ($arItem['PROPERTIES']['TYPE']['VALUE_XML_ID'] === 'video'): ?>
                <div class="banner__video video-block">
                    <video loop="" muted="" defaultmuted="" playsinline="" autoplay="">
                        <source src="<?= CFile::GetPath($arItem['PROPERTIES']['VIDEO_FILE']['VALUE']); ?>"
                                type="video/mp4">
                    </video>
                </div>
            <?php else: ?>
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>" class="banner__img banner__img_position">
            <?php endif; ?>
                <div class="banner__content">
                    <span class="banner__subtitle"><?=$arItem['PROPERTIES']['SUBTITLE']['VALUE']?></span>
                    <p class="banner__title"><?=$arItem['NAME']?></p>
                    <a href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>" class="banner__link link"><?=$arItem['PROPERTIES']['LINK']['DESCRIPTION']?></a>
                </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>