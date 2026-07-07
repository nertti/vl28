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
 * @var string $templateFolder
 */

//$this->setFrameMode(true);
//pr($arResult);
//pr($arResult['OFFERS'][0])
?>

<?php /** Начало карточки товара*/ ?>
    <section class="tovar">
        <div class="tovar__left">
            <div class="tovar__left-inner">
                <a href="/catalog/<?= $arResult['SECTION_CODE'] ?>" class="tovar__back">Назад</a>
                <span class="favorite-btn favor <?$APPLICATION->AddBufferContent("getFavoriteClass", $arResult['ID']);?>" data-item="<?= $arResult['ID'] ?>" data-item="<?= $arResult['ID'] ?>"></span>
            </div>
            <div class="swiper product-swiper">
				<div class="swiper-wrapper">
					<?php $slideIndex = 0; ?>
					
					<?php if (!empty($arResult['VIDEO_PATH_URL'])): ?>
						<div class="swiper-slide gallery-item">
							<video data-index="0" class="catalog-cart-video" autoplay muted playsinline loop src="<?= $arResult['VIDEO_PATH_URL'] ?>"></video>
						</div>
						<?php $slideIndex++; ?>
					<?php endif; ?>
			
					<?php foreach ($arResult['PROCESSED_GALLERY'] as $item): ?>
						<div class="swiper-slide gallery-item">
							<img data-index="<?= $slideIndex ?>" src="<?= $item['RESIZED_SRC'] ?>" width="<?= $item['WIDTH'] ?>" height="<?= $item['HEIGHT'] ?>" alt="Фото" loading="lazy">
						</div>
						<?php $slideIndex++; ?>
					<?php endforeach; ?>
				</div>
				<div class="swiper-pagination"></div>
					</div>
								<div class="images">
						<?php $galleryIndex = 0; ?>
						
						<?php if (!empty($arResult['VIDEO_PATH_URL'])): ?>
							<video class="catalog-cart-video gallery-item" data-index="0" autoplay muted playsinline loop src="<?= $arResult['VIDEO_PATH_URL'] ?>"></video>
							<?php $galleryIndex++; ?>
						<?php endif; ?>
					
						<?php foreach ($arResult['PROCESSED_GALLERY'] as $item): ?>
							<img src="<?= $item['ORIGINAL_SRC'] ?>" width="" height="" alt="Фото" loading="lazy" data-index="<?= $galleryIndex ?>" class="gallery-item">
							<?php $galleryIndex++; ?>
						<?php endforeach; ?>
					</div>
				</div>
        <div class="tovar__right">
            <?php

			 $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "breadcrumb",
                array(
                    "COMPONENT_TEMPLATE" => "breadcrumb",
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "-1"
                ),
                false
            ); 

			?>
            <div class="tovar__head">
                <h1 class="h2"><?= $arResult['NAME'] ?></h1>
				<!--<span class="favorite-btn favor <? //$APPLICATION->ShowViewContent('fav_class_'.$arResult['ID']);?>"></span>-->
<span class="favorite-btn favor <?$APPLICATION->AddBufferContent("getFavoriteClass", $arResult['ID']);?>" data-item="<?= $arResult['ID'] ?>"></span>
            </div>
            <?php if($arResult['OFFERS'][0]['PROPERTIES']['ARTICLE']['VALUE']):?>
            <div class="product-article" id="productArticle" style="margin-bottom: 10px">
                Артикул: <?= $arResult['OFFERS'][0]['PROPERTIES']['ARTICLE']['VALUE'] ?>
            </div>
            <?php endif;?>
            <?php
            $price = $arResult['OFFERS'][0]['ITEM_PRICES'][0];
            ?>

            <div class="price_wrapper">
                <p class="tovar__price" id="productPrice">
                    <?= $price['PRINT_PRICE'] ?>
                </p>

                <p
                        class="tovar__price tovar__price__wishoout__discont"
                        id="productOldPrice"
                        <?php if ($price['PRICE'] >= $price['BASE_PRICE']): ?>
                            style="display:none"
                        <?php endif; ?>
                >
                    <?= $price['PRINT_BASE_PRICE'] ?>
                </p>
            </div>
            <form action="#" class="tovar__form">
                <div class="tovar__color">
                    <div class="tovar__color-text">
                        <p class="tovar__color-title">Цвет</p>
                        <p class="tovar__color-current"><?= $arResult['CURRENT_COLOR'] ?></p>
                    </div>
                    <?php if (!empty($arResult['OTHER_COLORS'])): ?>
                        <div class="tovar__colors">
                            <?php foreach ($arResult['OTHER_COLORS'] as $value): ?>
                                <label class="tovar__color-item">
                                    <a href="<?= $value['LINK'] ?>"
                                       class="tovar__color-circle <?php if ($APPLICATION->GetCurPage() == $value['LINK']): ?>active<?php endif; ?>"
                                       title="<?= $value['ANCHOR'] ?>">
                                        <span style="background: #<?= $value['COLOR'] ?>;"></span>
                                    </a>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="tovar__colors">
                            <label class="tovar__color-item">
                                <span class="tovar__color-circle">
                                    <span style="background: #<?= $arResult['CURRENT_COLOR_XML'] ?>;"></span>
                                </span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                $offersData = [];
                foreach ($arResult['OFFERS'] as $index => $offer)
                {
                    $offersData[] = [
                            'ID' => $offer['ID'],
                            'SIZE' => $offer['PROPERTIES']['SIZE']['VALUE'],
                            'ARTICLE' => $offer['PROPERTIES']['ARTICLE']['VALUE'],
                            'PRICE' => $offer['ITEM_PRICES'][0]['PRINT_PRICE'],
                            'BASE_PRICE' => $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'],
                            'PRICE_VALUE' => $offer['ITEM_PRICES'][0]['PRICE'],
                            'BASE_PRICE_VALUE' => $offer['ITEM_PRICES'][0]['BASE_PRICE'],
                    ];
                }
                ?>
                <div class="tovar__size">
                    <div class="tovar__sizes">
                        <div class="title tovar__size-title">
                            Размер
                        </div>
                        <ul class="product-sizes">
                            <?php foreach ($offersData as $key => $offer): ?>

                                <li
                                        class="product-size-item <?= $key === 0 ? 'active' : '' ?>"
                                        data-offer-id="<?= $offer['ID'] ?>"
                                        data-size="<?= $offer['SIZE'] ?>"
                                        data-article="<?= htmlspecialcharsbx($offer['ARTICLE']) ?>"
                                        data-price="<?= htmlspecialcharsbx($offer['PRICE']) ?>"
                                        data-base-price="<?= htmlspecialcharsbx($offer['BASE_PRICE']) ?>"
                                        data-price-value="<?= $offer['PRICE_VALUE'] ?>"
                                        data-base-price-value="<?= $offer['BASE_PRICE_VALUE'] ?>"
                                >
                                    <?= $offer['SIZE'] ?>
                                </li>
                            <?php endforeach; ?>

                        </ul>

                    </div>

                    <a href="#" data-hystmodal="#sizeModal" class="tovar__size-btn">
                        Определить размер
                    </a>

                </div>
                <script>
                    let currentOfferId = <?= (int)$offersData[0]['ID'] ?>;
                </script>

                <?php if ($arResult['PROPERTIES']['AVAILABILITY']['VALUE'] !== 'Нет в наличии'): ?>
                    <a class="black-btn" id="addToBasket"
                       href="javascript:void(0);">
                        <span>Добавить в корзину</span>
                    </a>
                <?php else: ?>
                    <a class="black-btn" style="background-color: grey; cursor: not-allowed">
                        <span>Добавить в корзину</span>
                    </a>
                <?php endif; ?>
            </form>
            <a href="#" class="tovar__link" data-hystmodal="#descriptionModal">Описание</a>
            <a href="#" class="tovar__link" data-hystmodal="#howModal">Состав и уход</a>
            <a href="/customers/?code=delivery" class="tovar__link">Доставка и возврат</a>
        </div>
        <script>
            const offers = <?=CUtil::PhpToJSObject(array_map(function($offer){
                return [
                        'ID' => $offer['ID'],
                        'SIZE_ID' => $offer['TREE']['PROP_15'],
                        'SIZE' => $offer['PROPERTIES']['SIZE']['VALUE'],
                        'ARTICLE' => $offer['PROPERTIES']['ARTICLE']['VALUE'],
                        'PRICE' => $offer['ITEM_PRICES'][0]['PRINT_PRICE'],
                        'BASE_PRICE' => $offer['ITEM_PRICES'][0]['PRINT_BASE_PRICE'],
                        'PRICE_VALUE' => $offer['ITEM_PRICES'][0]['PRICE'],
                        'BASE_PRICE_VALUE' => $offer['ITEM_PRICES'][0]['BASE_PRICE'],
                ];
            }, $arResult['OFFERS']))?>;
        </script>
    </section>
    <script>
    </script>
<?php /** Конец карточки товара*/ ?>
<?php /** Начало модалки*/ ?>
    <!-- Модалка -->
    <div class="slider-modal" id="sliderModal">
        <button class="slider-close">✕</button>

        <!-- Основной слайдер -->
        <div class="swiper main-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>

                    <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                        <div class="swiper-slide">
                            <div class="swiper-zoom-container">
                                <video
                                        class="catalog-cart-video-modal"
                                        autoplay
                                        muted
                                        playsinline
                                        loop
                                        src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                                </video>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    $file = CFile::GetFileArray($imageId);
                    ?>

                    <div class="swiper-slide">
                        <div class="swiper-zoom-container">
                            <img src="<?= $file['SRC'] ?>" width="" height="" alt="Фото"
                                 loading="lazy">
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <!-- Стрелки -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <!-- Миниатюры -->
        <div class="swiper thumbs-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $index => $imageId): ?>
                    <?php if ($index === 0 && !empty($arResult['PROPERTIES']['VIDEO']['VALUE'])): ?>
                        <div class="swiper-slide">
                            <video
                                    class="catalog-cart-video-modal-mini"
                                    muted
                                    playsinline
                                    src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']) ?>">
                            </video>
                        </div>
                    <?php endif; ?>
                    <?php
                    $file = CFile::GetFileArray($imageId);
                    ?>
                    <div class="swiper-slide">
                        <img src="<?= $file['SRC'] ?>" width="" height="" alt="Фото" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('sliderModal');
            const closeBtn = modal.querySelector('.slider-close');

            const thumbsSwiper = new Swiper('.thumbs-swiper', {
                slidesPerView: 20,
                spaceBetween: 10,
                watchSlidesProgress: true,
            });

            const mainSwiper = new Swiper('.main-swiper', {
                spaceBetween: 10,
                thumbs: { swiper: thumbsSwiper },
                zoom: { maxRatio: 5 },

                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

                keyboard: {
                    enabled: true,
                    onlyInViewport: false,
                },
            });

            // ✅ Делегирование
            document.addEventListener('click', function (e) {
                //console.log(e)
                const item = e.target.closest('.gallery-item');
                if (!item) return;

                const index = Number(item.dataset.index ?? 0);

                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                mainSwiper.update();
                thumbsSwiper.update();
                mainSwiper.slideTo(index, 0);
            });

            closeBtn.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
<?php /** Конец модалки*/ ?>

<div class="hystmodal" id="sizeModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="sizes">
                <?php if (!empty($arResult['PROPERTIES']['DETERMINE']['VALUE']['TEXT'])): ?>
                    <?= html_entity_decode($arResult['PROPERTIES']['DETERMINE']['VALUE']['TEXT']) ?>
                <?php else: ?>
                    <?= $arResult['SECTION']['DESCRIPTION'] ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="howModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="how">
                <?php if (!empty($arResult['PROPERTIES']['INFO']['VALUE']['TEXT'])): ?>
                    <?= html_entity_decode($arResult['PROPERTIES']['INFO']['VALUE']['TEXT']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="descriptionModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <p class="h2">Информация о товаре</p>
            <div class="gray">
                <?= $arResult['DETAIL_TEXT'] ?>
            </div>
        </div>
    </div>
</div>

<div class="hystmodal" id="addBasketModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="thanks thanks-product">
                <div class="thanks-product__image">
                    <img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['NAME']?>">
                </div>
                <p class="h2">Товар добавлен в корзину!</p>
                <p class="thanks-product__name" id="basketModalName"></p>
                <a href="/basket/">Перейти в корзину</a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="errorBasketModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="thanks">
                <p class="h2">Не удалось добавить товар в корзину</p>
            </div>
        </div>
    </div>
</div>

<div class="hystmodal" id="addFavoriteModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks thanks-product" style="flex-direction: column">
                <div class="thanks-product__image">
                    <img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['NAME']?>">
                </div>
                <p class="h2">Товар добавлен в Избранное!</p>
                <a href="/profile/favorite/" class="">Всё избранное</a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="delFavoriteModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks thanks-product">
                <div class="thanks-product__image">
                    <img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="<?=$arResult['NAME']?>">
                </div>
                <p class="h2">Товар удалён из Избранного!</p>
            </div>
        </div>
    </div>
</div>
<?php
unset($actualItem, $itemIds, $jsParams);
?>

