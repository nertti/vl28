<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
$isMainPage = $APPLICATION->GetCurPage(false) === '/';

?>
<!doctype html>
<html lang="ru">
<head>
    <?php Asset::getInstance()->addString("<meta name=’viewport’ content=’width=device-width, initial-scale=1’>")?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
    <?php
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/assets/css/hystmodal.min.css');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/assets/css/swiper-bundle.min.css');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/assets/css/main.min.css');
    ?>
    <?php $APPLICATION->ShowHead(); ?>
</head>
<body>
<?php //$APPLICATION->ShowPanel(); ?>
<!-- header -->
<header class="header">
    <div class="header__left">
        <a href="#" data-hystmodal="#menuModal" class="menu-btn">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/menu.svg" alt="menu icon" class="menu-btn__icon svg">
            <span class="menu-btn__text">Меню</span>
        </a>
        <form action="#" role="search" method="get" class="search-form">
            <input type="submit" class="search-form__button" value="Искать">
            <input type="search" class="search-form__input" placeholder="Поиск" value="" name="s">
        </form>
    </div>
    <a href="/" class="header__logo logo">
        <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
        <a href="#" class="header__link">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/favorite.svg" alt="favorite icon" class="header__link-icon svg">
        </a>
        <a href="#" class="header__link">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/user.svg" alt="user icon" class="header__link-icon svg">
        </a>
        <a href="#" class="header__link">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/cart.svg" alt="cart icon" class="header__link-icon header__link-icon_cart svg">
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
                            "MENU_CACHE_GET_VARS" => array(
                            ),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "Y",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "top",
                            "USE_EXT" => "N"
                        ),
                        false
                    ); ?>
                </ul>
                <form action="#" role="search" method="get" class="search-form">
                    <input type="submit" class="search-form__button" value="Искать">
                    <input type="search" class="search-form__input" placeholder="Поиск" value="" name="s">
                </form>
            </div>
        </div>
    </div>
</div>
<main>
<?php
$APPLICATION->IncludeComponent("bitrix:news.list", "main_top_banners", array(
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
),
    false
); ?>