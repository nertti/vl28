<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
/** @var \CUser $USER */

if ($USER->IsAuthorized()) {
    header('Location: /profile/');
    exit();
}
?>
<!doctype html>
<html lang="ru">
<head>
    <?php Asset::getInstance()->addString("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>") ?>
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
        <a href="#" class="header__link" style="display: none">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt="favorite icon"
                 class="header__link-icon svg">
        </a>
        <a href="#" class="header__link" style="display: none">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/user.svg" alt="user icon" class="header__link-icon svg">
        </a>
        <a href="#" class="header__link" style="display: none">
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