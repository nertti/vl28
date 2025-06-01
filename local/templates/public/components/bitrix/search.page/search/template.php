<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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
?>

<div class="search-modal search-page">
    <form action="" role="search" method="get" class="search-form">
        <input class="search-form__input" placeholder="Введите что вы хотите найти?" type="text" name="q"
               value="<?= $arResult['REQUEST']['QUERY'] ?>">
        <input type="submit" class="main-btn" value="Найти">
    </form>
</div>
<section class="products products_catalog">
    <div class="products__list">
        <?php if (count($arResult['SEARCH']) > 0): ?>
            <?php foreach ($arResult['SEARCH'] as $arItem): ?>

                <a href="<?= $arItem['URL_WO_PARAMS'] ?>"
                   class="product">

                    <?php if (!empty($arItem['IMAGES'])): ?>
                        <div class="product__swiper swiper" id="product<?= $arItem['ID'] ?>">
                            <div id="swiper-wrapper-<?= $arItem['ID'] ?>" class="swiper-wrapper">
                                <?php foreach ($arItem['IMAGES']['VALUE'] as $image): ?>
                                    <div class="swiper-slide">
                                        <img src="<?= CFile::GetPath($image); ?>" alt="<?= $arItem['TITLE'] ?>"
                                             class="product__img">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-button-prev">
                                <svg class="arrow">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                                </svg>
                            </div>
                            <div class="swiper-button-next">
                                <svg class="arrow">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                                </svg>
                            </div>
                            <div class="swiper-scrollbar"></div>
                        </div>
                    <?php endif; ?>

                    <div class="product__inner">
                        <p class="product__title"><?= $arItem['TITLE'] ?></p>
                        <p class="product__price"><?= $arItem['PRICE'] ?> ₽</p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="search-modal">
            <p class="h2">Ничего не нашлось...</p>
            <p>Проверьте, правильно ли введен запрос</p>
        </div>
        <?php endif; ?>
    </div>
</section>

