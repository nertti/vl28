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
<header class="header">
    <div class="header__left"></div>
    <a href="/" class="header__logo logo">
        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right"></div>
</header>
<main>