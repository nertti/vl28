<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Профиль");

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();
//pr($arUser);
?>
    <div class="container">
        <div class="account__wrap">
            <div class="account__left">
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "profile", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "left",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </div>
            <div class="account__right">
                <div class="account__default">
                    <div class="account__orders account__orders_border">
                        <p class="account__title">Мои заказы</p>
                        <p>Активных заказов пока нет</p>
                    </div>
                    <div class="account__loyal">
                        <p class="account__title">Карта лояльности</p>
                        <div class="account__loyal-card">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Light">
                            <div class="account__loyal-inner">
                                <p>Уровень карты: <strong>Light</strong></p>
                                <p>Доступные бонусы: <strong>698 баллов</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="account__data">
                        <p class="account__title">Личные данные</p>
                        <div class="account__data-list">
                            <p><?=$arUser['LAST_NAME']." ".$arUser['NAME']?></p>
                            <p><?=$arUser['PERSONAL_PHONE']?></p>
                            <p><?=$arUser['EMAIL']?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>