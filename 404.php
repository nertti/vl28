<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

/** @var \CMain $APPLICATION */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
?>
    <section class="s404">
        <p class="s404__big">404</p>
        <p class="h2">Страница не найдена</p>
        <p class="s404__text">
            К сожалению, мы не смогли найти страницу, которую вы искали.<br>
            Пожалуйста, вернитесь на нашу домашнюю страницу, чтобы продолжить просмотр нашего сайта.
        </p>
        <a href="/" class="main-btn main-btn_white">На главную страницу</a>
    </section>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>