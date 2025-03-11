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

    <div class="hystmodal" id="loyalModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_loyal" role="dialog" aria-modal="true">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="loyal">
                    <p class="h2">Программа лояльности</p>
                    <div class="loyal__table">
                        <div class="loyal__row">
                            <div class="loyal__item">Вид карты</div>
                            <div class="loyal__item">
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/card1.svg" alt="Карта">
                                Light
                            </div>
                            <div class="loyal__item">
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/card2.svg" alt="Карта">
                                Highlight
                            </div>
                            <div class="loyal__item">
                                <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/card3.svg" alt="Карта">
                                Luxury
                            </div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Условия получения</div>
                            <div class="loyal__item">Бесплатно при оформлении заявки</div>
                            <div class="loyal__item">При сумме покупок от  75 000 ₽</div>
                            <div class="loyal__item">При сумме покупок от  150 000 ₽</div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Срок действия</div>
                            <div class="loyal__item">1 год</div>
                            <div class="loyal__item">1 год</div>
                            <div class="loyal__item">1 год</div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Начисление бонусов</div>
                            <div class="loyal__item">5 %</div>
                            <div class="loyal__item">10 %</div>
                            <div class="loyal__item">15 %</div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Возможность 100% оплаты бонусами</div>
                            <div class="loyal__item">Да, только в&nbsp;интернет-магазине</div>
                            <div class="loyal__item">Да</div>
                            <div class="loyal__item">Да</div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Бонусы в&nbsp;день рождения</div>
                            <div class="loyal__item">Дополнительно&nbsp;10% *</div>
                            <div class="loyal__item">Дополнительно&nbsp;10% *</div>
                            <div class="loyal__item">Дополнительно&nbsp;10% *</div>
                        </div>
                        <div class="loyal__row">
                            <div class="loyal__item">Скидка на&nbsp;доставку из&nbsp;интернет-магазина</div>
                            <div class="loyal__item">Бесплатная доставка</div>
                            <div class="loyal__item">Бесплатная доставка</div>
                            <div class="loyal__item">Бесплатная доставка</div>
                        </div>
                    </div>
                    <div class="loyal__mobile">
                        <div class="loyal__nav">
                            <div class="loyal__nav-item active">Light</div>
                            <div class="loyal__nav-item">Highlight</div>
                            <div class="loyal__nav-item">Luxury</div>
                        </div>
                        <div class="loyal__tabs">
                            <div class="loyal__tab active">
                                <div class="loyal__row">
                                    <p>Условия получения</p>
                                    <p>Бесплатно при оформлении заявки</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Срок действия</p>
                                    <p>1 год</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Начисление бонусов</p>
                                    <p>5 %</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Возможность 100% оплаты бонусами</p>
                                    <p>Да, только в&nbsp;интернет-магазине</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Бонусы в&nbsp;день рождения</p>
                                    <p>Дополнительно&nbsp;10% *</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Скидка на&nbsp;доставку из&nbsp;интернет-магазина</p>
                                    <p>Бесплатная доставка</p>
                                </div>
                            </div>
                            <div class="loyal__tab" style="display: none;">
                                <div class="loyal__row">
                                    <p>Условия получения</p>
                                    <p>При сумме покупок от  75 000 ₽</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Срок действия</p>
                                    <p>1 год</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Начисление бонусов</p>
                                    <p>10 %</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Возможность 100% оплаты бонусами</p>
                                    <p>Да</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Бонусы в&nbsp;день рождения</p>
                                    <p>Дополнительно&nbsp;10% *</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Скидка на&nbsp;доставку из&nbsp;интернет-магазина</p>
                                    <p>Бесплатная доставка</p>
                                </div>
                            </div>
                            <div class="loyal__tab" style="display: none;">
                                <div class="loyal__row">
                                    <p>Условия получения</p>
                                    <p>При сумме покупок от  150 000 ₽</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Срок действия</p>
                                    <p>1 год</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Начисление бонусов</p>
                                    <p>15 %</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Возможность 100% оплаты бонусами</p>
                                    <p>Да</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Бонусы в&nbsp;день рождения</p>
                                    <p>Дополнительно&nbsp;10% *</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Скидка на&nbsp;доставку из&nbsp;интернет-магазина</p>
                                    <p>Бесплатная доставка</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="loyal__small">* - На один заказ за 7 дней до и 7 дней после дня рождения </p>
                    <a href="#" class="loyal__link">Регламент использования</a>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>