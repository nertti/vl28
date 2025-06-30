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

$currentPage = $APPLICATION->GetCurPage();
?>

<section class="catalog first-section top0">
    <div class="container">
        <div class="catalog__settings">
            <div class="category">
                <a href="/catalog/" class="category__item<?php if ($currentPage === '/catalog/'):?> category__item_current<?php endif;?>">Все изделия</a>
                <?php foreach ($arResult['SECTIONS'] as $section):?>
                    <a href="<?=$section['SECTION_PAGE_URL']?>"
                        class="category__item<?php if ($currentPage == $section['SECTION_PAGE_URL']):?> category__item_current<?php endif;?>">
                        <?=$section['NAME']?></a>
                <?php endforeach;?>
            </div>
            <a href="#" data-hystmodal="#filterModal" class="filter-btn">Фильтры</a>
        </div>
    </div>
</section>
