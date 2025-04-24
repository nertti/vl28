<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Карта лояльности");

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();
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
                    <div class="account__card">
                        <p>Ваша карта</p>

                        <div class="account__card-wrap">
                            <div class="account__card-img">
                                <span class="account__card-type">Light</span>
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/card1.svg" alt="Карта">
                                <span class="account__card-count">698 баллов</span>
                            </div>
                            <div class="account__card-params">
                                <p>Уровень карты: <strong>Light</strong></p>
                                <p>Доступные бонусы: <strong>698 баллов</strong></p>
                                <p>Ближайшая дата сгорания бонусов: <strong>20.11.2025</strong></p>
                                <p>До следующего уровня (Highlight) осталось: <strong>65041 ₽</strong></p>
                            </div>
                        </div>

                        <div class="account__card-footer">
                            <p>
                                5% от стоимости покупки возвращается на Вашу карту лояльности на 15 день после получения заказа
                            </p>
                            <a href="#" data-hystmodal="#loyalModal" class="black-btn">Подробнее о программе лояльности</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>