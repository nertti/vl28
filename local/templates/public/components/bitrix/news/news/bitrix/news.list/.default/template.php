<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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
<?php foreach ($arResult["ITEMS"] as $key => $arItem):
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>

    <?php if ($key % 3 == 0): ?>
        <div class="blog__list bottom40">
    <?php endif; ?>

    <a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="article">
        <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>" class="article__img">
        <div class="article__inner">
            <p class="article__title"><?= $arItem['NAME'] ?></p>
            <p class="article__desc">
                <?= $arItem['~PREVIEW_TEXT'] ?>
            </p>
            <span class="article__date"><?= $arItem['ACTIVE_FROM'] ?></span>
        </div>
    </a>

    <?php if (($key + 1) % 3 == 0 || ($key + 1) == count($arResult["ITEMS"])): ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
