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
<div class="hystmodal" id="filterModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="filter">
                <a href="#" class="filter__clear">очистить фильтры</a>
                <form action="#" class="filter__form">
                    <div class="filter__wrap">
                        <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                        <?php if (empty($arItem['DISPLAY_TYPE'])){continue;}?>
                            <div class="filter__block">
                                <p class="filter__name"><?= $arItem['NAME'] ?></p>
                                <div class="filter__list <?php if ($arItem['DISPLAY_TYPE'] == 'K'): ?>filter__list_row<?php endif; ?>">
                                    <?php foreach ($arItem['VALUES'] as $key => $value): ?>
                                        <?php if ($arItem['DISPLAY_TYPE'] == 'F'): ?>
                                            <label class="filter__item">
                                                <input type="checkbox" name="collections"
                                                       value="<?= $value['URL_ID'] ?>">
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php elseif ($arItem['DISPLAY_TYPE'] == 'H'): ?>
                                            <label class="filter__item filter__item_color">
                                                <input type="checkbox" name="color" value="<?= $key ?>">
                                                <span class="checkmark">
                                                    <span class="checkmark__color"
                                                          style="background: <?= $value['URL_ID'] ?>;"></span>
                                                </span>
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php elseif ($arItem['DISPLAY_TYPE'] == 'K'): ?>
                                            <label class="filter__item">
                                                <input type="checkbox" name="size" value="<?= $value['URL_ID'] ?>">
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="submit" class="main-btn" value="Применить фильтры">
                </form>
            </div>
        </div>
    </div>
</div>
