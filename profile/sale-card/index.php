<?php

/** @var \CMain $APPLICATION */
/** @var $userBonus */
/** @var $totalPaid */
/** @var $discountPercent */
/** @var $discountCard */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/profile/sale.php");

use Bitrix\Main\Loader;

Loader::includeModule("sale");
Loader::includeModule("catalog");
CModule::IncludeModule('iblock');

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();

$APPLICATION->SetTitle("Карта лояльности");
?>
    <div class="container">
        <div class="account__wrap">
            <div class="account__left">
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "profile", array(
                    "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",    // Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",    // Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",    // Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",    // Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",    // Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",    // Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",    // Учитывать права доступа
                    "ROOT_MENU_TYPE" => "left",    // Тип меню для первого уровня
                    "USE_EXT" => "N",    // Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </div>
            <div class="account__right">
                <div class="account__default">
                    <div class="account__card">
                        <p>Ваша карта</p>

                        <div class="account__card-wrap">
                            <a href="#" data-hystmodal="#loyalModal" class="account__card-img" data-type="<?= $discountCard ?>">
                                <span class="account__card-type"><?= $discountCard ?></span>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Карта">
                                <span class="account__card-count"><?= $userBonus ?> баллов</span>
                            </a>
                            <div class="account__card-params">
                                <p>Уровень карты: <strong><?= $discountCard ?></strong></p>
                                <p>Доступные бонусы: <strong><?= $userBonus ?> баллов</strong></p>
                                <?php if ($totalPaid < 75000): ?>
                                    <p>До следующего уровня (Highlight) осталось: <strong><?= -$totalPaid + 75000 ?>
                                            ₽</strong></p>
                                <?php elseif ($totalPaid > 75000 && $totalPaid < 149999): ?>
                                    <p>До следующего уровня (Luxury) осталось: <strong><?= -$totalPaid + 150000 ?>
                                            ₽</strong></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="account__card-footer">
                            <p>
                                <?= $discountPercent ?>% от стоимости покупки возвращается на Вашу карту лояльности на
                                15 день после получения
                                заказа
                            </p>
                            <a href="#" data-hystmodal="#loyalModal" class="black-btn">Подробнее о программе
                                лояльности</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cardImg = document.querySelector('.account__card-img');
            cardImg.addEventListener('click', handleCardClick);
            let loyalItem = document.querySelectorAll('.loyal__nav-item');

            function handleCardClick(event) {
                event.preventDefault();

                let cardType = cardImg.dataset.type;

                // Определяем номер таба на основе типа карты
                let tabIndex;
                switch(cardType) {
                    case 'Light':
                        tabIndex = 0;
                        break;
                    case 'Highlight':
                        tabIndex = 1;
                        break;
                    case 'Luxury':
                        tabIndex = 2;
                        break;
                    default:
                        return;
                }

                // Удаляем активный класс со всех табов
                loyalItem.forEach(item => {
                    item.classList.remove('active');
                });

                // Добавляем активный класс к нужному табу
                loyalItem[tabIndex].classList.add('active');
            }
        });
    </script>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>