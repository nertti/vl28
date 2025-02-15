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
    <section class="client top0">
        <div class="container">
            <div class="client__wrap">
                <div class="client__nav">
                    <?php $count = 0; ?>
                    <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                        <?php $count++; ?>
                        <div class="client__nav-item h2 <?php if ($count === 1): ?>active<?php endif; ?>"><?= $arItem['NAME'] ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="client__tabs">
                    <?php $count = 0; ?>
                    <?php foreach ($arResult["ITEMS"] as $arItem): ?>
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                        ?>
                        <?php $count++; ?>
                        <div id="<?= $this->GetEditAreaId($arItem['ID']); ?>"
                             class="client__tab <?php if ($count === 1): ?>active<?php endif; ?>">
                            <?= $arItem["DETAIL_TEXT"]; ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </section>
<?php endif; ?>