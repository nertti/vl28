<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
$isMainPage = $APPLICATION->GetCurPage(false) === '/';
$isAboutPage = $APPLICATION->GetCurPage(false) === '/about/';
?>
<!doctype html>
<html lang="ru">
<head>
    <?php Asset::getInstance()->addString("<meta name=’viewport’ content=’width=device-width, initial-scale=1’>") ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
    <?php
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/hystmodal.min.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/swiper-bundle.min.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/main.min.css');
    ?>
    <?php $APPLICATION->ShowHead(); ?>
</head>
<body>
<?php $APPLICATION->ShowPanel(); ?>
<!-- header -->
<header class="header">
    <div class="header__left">
        <a href="#" data-hystmodal="#menuModal" class="menu-btn">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/menu.svg" alt="menu icon" class="menu-btn__icon svg">
            <span class="menu-btn__text">Меню</span>
        </a>
        <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
            "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
            "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
        ),
            false
        ); ?>
    </div>
    <a href="/" class="header__logo logo">
        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
        <a href="#" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt="favorite icon"
                 class="header__link-icon svg">
        </a>
        <a href="#" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/user.svg" alt="user icon" class="header__link-icon svg">
        </a>
        <a href="#" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/cart.svg" alt="cart icon"
                 class="header__link-icon header__link-icon_cart svg">
        </a>
    </div>
</header>
<!-- header-end -->

<div class="hystmodal hystmodal_menu" id="menuModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="modal-menu">
                <ul>
                    <?php $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top",
                        array(
                            "ALLOW_MULTI_SELECT" => "N",
                            "CHILD_MENU_TYPE" => "left",
                            "COMPONENT_TEMPLATE" => "top",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "Y",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "top",
                            "USE_EXT" => "N"
                        ),
                        false
                    ); ?>
                </ul>
                <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
                    "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
                    "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
                ),
                    false
                ); ?>
            </div>
        </div>
    </div>
</div>
<main>
    <?php if ($isMainPage): ?>
        <?php $APPLICATION->IncludeComponent("bitrix:news.list", "main_top_banners", array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
            "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
            "AJAX_MODE" => "N",    // Включить режим AJAX
            "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
            "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
            "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
            "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
            "CACHE_FILTER" => "N",    // Кешировать при установленном фильтре
            "CACHE_GROUPS" => "Y",    // Учитывать права доступа
            "CACHE_TIME" => "360000",    // Время кеширования (сек.)
            "CACHE_TYPE" => "A",    // Тип кеширования
            "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
            "DISPLAY_BOTTOM_PAGER" => "N",    // Выводить под списком
            "DISPLAY_DATE" => "N",    // Выводить дату элемента
            "DISPLAY_NAME" => "Y",    // Выводить название элемента
            "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
            "DISPLAY_PREVIEW_TEXT" => "N",    // Выводить текст анонса
            "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
            "FIELD_CODE" => array(    // Поля
                0 => "",
                1 => "",
            ),
            "FILTER_NAME" => "",    // Фильтр
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
            "IBLOCK_ID" => "1",    // Код информационного блока
            "IBLOCK_TYPE" => "rest_entity",    // Тип информационного блока (используется только для проверки)
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
            "INCLUDE_SUBSECTIONS" => "Y",    // Показывать элементы подразделов раздела
            "MEDIA_PROPERTY" => "",
            "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
            "NEWS_COUNT" => "2",    // Количество новостей на странице
            "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
            "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
            "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
            "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
            "PAGER_TEMPLATE" => "",    // Шаблон постраничной навигации
            "PAGER_TITLE" => "",    // Название категорий
            "PARENT_SECTION" => "1",    // ID раздела
            "PARENT_SECTION_CODE" => "",    // Код раздела
            "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
            "PROPERTY_CODE" => array(    // Свойства
                0 => "TYPE",
                1 => "LINK",
                2 => "ADDITIONAL_LINK",
                3 => "SUBTITLE",
            ),
            "SEARCH_PAGE" => "/search/",
            "SET_BROWSER_TITLE" => "N",    // Устанавливать заголовок окна браузера
            "SET_LAST_MODIFIED" => "N",    // Устанавливать в заголовках ответа время модификации страницы
            "SET_META_DESCRIPTION" => "N",    // Устанавливать описание страницы
            "SET_META_KEYWORDS" => "N",    // Устанавливать ключевые слова страницы
            "SET_STATUS_404" => "N",    // Устанавливать статус 404
            "SET_TITLE" => "N",    // Устанавливать заголовок страницы
            "SHOW_404" => "N",    // Показ специальной страницы
            "SLIDER_PROPERTY" => "",
            "SORT_BY1" => "SORT",    // Поле для первой сортировки новостей
            "SORT_BY2" => "ACTIVE_FROM",    // Поле для второй сортировки новостей
            "SORT_ORDER1" => "ASC",    // Направление для первой сортировки новостей
            "SORT_ORDER2" => "DESC",    // Направление для второй сортировки новостей
            "STRICT_SECTION_CHECK" => "Y",    // Строгая проверка раздела для показа списка
            "USE_RATING" => "N",
            "USE_SHARE" => "N"
        ), false); ?>
        <section class="products">
            <p class="h2">Новые поступления</p>
            <div class="products__list products__list_home">
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product1">
                        <div class="swiper-wrapper" id="swiper-wrapper-a03ca10111a1108a3" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product1.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product1.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-a03ca10111a1108a3">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-a03ca10111a1108a3">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product2">
                        <div class="swiper-wrapper" id="swiper-wrapper-58b105766a77a4457" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product2.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product2.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-58b105766a77a4457">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-58b105766a77a4457">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">T-SHIRT DENSE WHITE</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product3">
                        <div class="swiper-wrapper" id="swiper-wrapper-b8e61fd49678fb54" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product3.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product3.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-b8e61fd49678fb54">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-b8e61fd49678fb54">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">ZIP-HOODIE</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
            </div>
            <a href="#" class="products__link link">Перейти в каталог</a>
        </section>
        <section class="content">
            <?php $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "main_second_banners",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "360000",
                    "CACHE_TYPE" => "A",
                    "CHECK_DATES" => "Y",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "DETAIL_URL" => "",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "DISPLAY_DATE" => "N",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "FILTER_NAME" => "",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "IBLOCK_ID" => "1",
                    "IBLOCK_TYPE" => "rest_entity",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "MEDIA_PROPERTY" => "",
                    "MESSAGE_404" => "",
                    "NEWS_COUNT" => "2",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "PAGER_TITLE" => "",
                    "PARENT_SECTION" => "2",
                    "PARENT_SECTION_CODE" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "PROPERTY_CODE" => array(
                        0 => "TYPE",
                        1 => "LINK",
                        2 => "SUBTITLE",
                        3 => "ADDITIONAL_LINK",
                        4 => "",
                    ),
                    "SEARCH_PAGE" => "/search/",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SLIDER_PROPERTY" => "",
                    "SORT_BY1" => "SORT",
                    "SORT_BY2" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "ASC",
                    "SORT_ORDER2" => "DESC",
                    "STRICT_SECTION_CHECK" => "Y",
                    "USE_RATING" => "N",
                    "USE_SHARE" => "N",
                    "COMPONENT_TEMPLATE" => "main_second_banners"
                ),
                false
            ); ?>
            <div class="container">
                <?php $APPLICATION->IncludeFile(
                    "/include/main/under_second_banners.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
            </div>
        </section>
        <section class="content">
            <?php $APPLICATION->IncludeFile(
                "/include/main/single_banner.php",
                array(),
                array(
                    "MODE" => "text"
                )
            ); ?>
            <div class="container">
                <?php $APPLICATION->IncludeFile(
                    "/include/main/under_single_banner.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
            </div>
        </section>
        <section class="blog">
          <?php $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"main_news", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "360000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "rest_entity",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MEDIA_PROPERTY" => "",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "3",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_TITLE" => "",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "SOURCE",
			1 => "",
			2 => "",
			3 => "",
			4 => "",
			5 => "",
		),
		"SEARCH_PAGE" => "/search/",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SLIDER_PROPERTY" => "",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "Y",
		"USE_RATING" => "N",
		"USE_SHARE" => "N",
		"COMPONENT_TEMPLATE" => "main_news"
	),
	false
); ?>
        </section>
    <?php else:?>
        <?php if ($isAboutPage):?>
            <section class="banner">
              <div class="banner__video video-block">
                <video loop="" muted="" defaultmuted="" playsinline="" autoplay="">
                  <source src="<?=SITE_TEMPLATE_PATH?>/assets/img/banner1.mp4" type="video/mp4">
                </video>
              </div>
              <div class="banner__content banner__content_space">
                <p class="banner__title">
                <?php $APPLICATION->IncludeFile(
                    "/include/about/title_banner.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
                </p>
              </div>
            </section>
        <?php endif;?>
    <div class="container top40">
        <?php $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "breadcrumb",
            array(
                "COMPONENT_TEMPLATE" => "breadcrumb",
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "-1"
            ),
            false
        ); ?>
        <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
    </div>
    <?php endif; ?>