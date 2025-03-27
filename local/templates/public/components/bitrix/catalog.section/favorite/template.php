<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |    Attention!
 * |    The following comments are for system use
 * |    and are required for the component to work correctly in ajax mode:
 * |    <!-- items-container -->
 * |    <!-- pagination-container -->
 * |    <!-- component-end -->
 */

$this->setFrameMode(true);
?>

<div class="account__favorite">
<?php if (!empty($arResult["ITEMS"])):?>
    <?php foreach ($arResult["ITEMS"] as $cell => $arElement): ?>
        <?php
        $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?php
        $res = CIBlockSection::GetByID($arElement['IBLOCK_SECTION_ID']);
        $arRes = $res->GetNext();
        ?>
        <div id="<?= $this->GetEditAreaId($arElement['ID']); ?>" class="product">
            <span class="product__favorite product__favorite_active favor active" data-item='<?=$arElement['ID']?>'></span>
            <img src="<?= $arElement['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arElement['NAME'] ?>" class="product__img">
            <a href="/catalog/<?=$arRes['CODE']?>/<?=$arElement['CODE']?>/" class="product__inner">
                <p class="product__title"><?= $arElement['NAME'] ?></p>
                <p class="product__price"><?=round($arElement['OFFERS'][2]['PRICES']['BASE']['VALUE_VAT'])?> ₽</p>
            </a>
        </div>
    <?php endforeach; ?>
    <?php else:?>
        <p>В Вашем избранном пусто</p>
    <?php endif;?>
</div>