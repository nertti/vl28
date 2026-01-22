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

<section class="products products_catalog">
    <div class="products__list">
        <?php foreach ($arResult["ITEMS"] as $cell => $arElement): ?>
            <?php
            $this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));
            ?>
            <a id="<?= $this->GetEditAreaId($arElement['ID']); ?>" href="<?= $arElement["DETAIL_PAGE_URL"] ?>"
               class="product">
                <?php if (!empty($arElement['PROPERTIES']['IMAGES']['VALUE'])): ?>
                    <div class="product__swiper swiper" id="product<?= $cell ?>">
                        <div id="swiper-wrapper-<?= $cell ?>" class="swiper-wrapper">
                            <div class="swiper-slide">
                                <?php
                                $mainImage = getResponsiveImage($arElement['DETAIL_PICTURE']['ID']);
                                ?>
                                <?php if ($mainImage): ?>
                                    <img
                                            src="<?= $mainImage['src'] ?>"
                                            srcset="<?= $mainImage['srcset'] ?>"
                                            sizes="<?= $mainImage['sizes'] ?>"
                                            alt="<?= htmlspecialchars($arElement['NAME']) ?>"
                                            class="product__img"
                                            loading="lazy"
                                    >
                                <?php endif; ?>
                            </div>
                            <?php foreach ($arElement['PROPERTIES']['IMAGES']['VALUE'] as $imageId): ?>
                                <?php $img = getResponsiveImage($imageId); ?>
                                <?php if ($img): ?>
                                    <div class="swiper-slide">
                                        <img
                                                src="<?= $img['src'] ?>"
                                                srcset="<?= $img['srcset'] ?>"
                                                sizes="<?= $img['sizes'] ?>"
                                                alt="<?= htmlspecialchars($arElement['NAME']) ?>"
                                                class="product__img"
                                                loading="lazy"
                                        >
                                    </div>
                                <?php endif; ?>
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
                    <p class="product__title"><?= $arElement['NAME'] ?></p>
                    <p class="product__price"><?=round($arElement['PRICE']['PRICE'])?> ₽</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>


<?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <?= $arResult["NAV_STRING"] ?>
<?php endif; ?>
