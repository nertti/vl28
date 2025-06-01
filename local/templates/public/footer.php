<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
/** @var bool $isMainPage */
?>
</main>
<footer class="footer">
    <div class="footer__main">
        <nav class="footer__nav">
            <ul class="footer__list">
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </ul>
            <ul class="footer__list">
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom1",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </ul>
            <ul class="footer__list">
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom2",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </ul>
            <ul class="footer__list">
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom3",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </ul>
            <ul class="footer__list">
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom4",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </ul>
        </nav>
        <div class="subs">
            <p class="subs__text">Будьте в курсе всех новинок и специальных предложений</p>
            <a href="#" class="subs__btn main-btn" data-hystmodal="#subModal">Подписаться</a>
        </div>
    </div>
    <div class="footer__bottom">
        <a href="/" class="footer__logo logo">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.svg" alt="VL28" class="logo__img">
        </a>
        <span>© VL28, 2024. Все права защищены</span>
    </div>
</footer>
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
<div class="hystmodal" id="subModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="subscribe">
                <p class="h2">Подписка на новости</p>
                <form id="subscription" action="/ajax/subscription.php" class="subscribe__form">
                    <div class="subscribe__form-inner">
                        <p class="subscribe__form-label">E-mail</p>
                        <div class="subscribe__form-col">
                            <div class="input-wrap">
                                <p class="error-text" style="display: none">* Некорректный адрес электронной почты</p>
                                <input type="text" class="form-input" name="EMAIL" placeholder="banshin@yandex.ru">
                            </div>
                            <span>
                  Нажимая кнопку «подписаться», вы даёте согласие на рекламную рассылку и обработку персональных данных в соответствии с&nbsp;правилами
                </span>
                        </div>
                    </div>
                    <input type="submit" class="main-btn" value="Подписаться на наши новости">
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const subscriptionForm = document.querySelector('form[id="subscription"]');
                        // Если форма найдена, добавляем слушатель события submit
                        if (subscriptionForm) {
                            subscriptionForm.addEventListener('submit', handleFormSubmit);
                        } else {
                            console.warn('Форма не найдена на странице');
                        }

                        function handleFormSubmit(event) {
                            event.preventDefault();
                            // Собираем данные формы
                            const formData = new FormData(subscriptionForm);
                            // Отправляем AJAX-запрос
                            fetch(subscriptionForm.action, {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    let errorSubError = document.querySelector('.subscribe .error-text');
                                    let errorSubInput = document.querySelector('.subscribe .form-input');
                                    if (data.status === 'error') {
                                        // Сначала убираем все предыдущие ошибки
                                        errorSubInput.classList.remove('error');
                                        errorSubError.style.display = 'none';

                                        // Проверяем обе ошибки и показываем первую найденную
                                        if (data.message.EMAIL || data.message.SUB) {
                                            errorSubInput.classList.add('error');
                                            errorSubError.style.display = 'block';
                                            errorSubError.innerHTML = data.message.EMAIL || data.message.SUB;
                                        }
                                    } else {
                                        const subModal = document.querySelector('#subModal');
                                        const thanksModal = document.querySelector('#thanksModal');

                                        if (subModal) {
                                            subModal.querySelector('.hystmodal__close').click();
                                        } else {
                                            console.error('Модальное окно subModal не найдено');
                                        }
                                        if (thanksModal) {
                                            subModal.querySelector('#openThanksModal').click();
                                        } else {
                                            console.error('Модальное окно thanksModal не найдено');
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Ошибка при отправке формы:', error);
                                });
                        }
                    });
                </script>
            </div>
        </div>
    </div>

</div>
<div class="hystmodal" id="thanksModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks">
                <p class="h2">Данные успешно отправлены. Спасибо за подписку!</p>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="addFavoriteModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="thanks" style="flex-direction: column">
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
            <div class="thanks">
                <p class="h2">Товар удалён из Избранного!</p>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="searchModal" aria-hidden="true">
    <div class="hystmodal__wrap hystmodal_search">
        <div class="hystmodal__window hystmodal__window_search" role="dialog" aria-modal="true">
            <div class="search-modal">
                <p class="search-modal__title">Поиск</p>
                <form action="#" role="search" method="get" class="search-form">
                    <input type="search" class="search-form__input" placeholder="Введите что вы хотите найти?" value="" name="s">
                    <input type="submit" class="main-btn" value="Найти">
                </form>
                <button data-hystclose class="hystmodal__close">Закрыть</button>
                <div class="search-modal__wrap">
                    <div class="search-modal__left">
                        <p class="h2">Недавние запросы</p>
                        <p>джинсы</p>
                        <p>футболка</p>
                    </div>
                    <div class="search-modal__right">
                        <p class="h2">Результат</p>
                        <div class="swiper-button-prev">
                            <svg class="arrow">
                                <use xlink:href="assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next">
                            <svg class="arrow">
                                <use xlink:href="assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="search-modal__swiper swiper">
                            <div class="swiper-wrapper">
                                <a href="#" class="swiper-slide product">
                                    <img src="assets/img/export/product1.webp" alt="T-SHIRT DENSE BLACK 100" class="product__img">
                                    <div class="product__inner">
                                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                                        <p class="product__price">8 900 ₽</p>
                                    </div>
                                </a>
                                <a href="#" class="swiper-slide product">
                                    <img src="assets/img/export/product1.webp" alt="T-SHIRT DENSE BLACK 100" class="product__img">
                                    <div class="product__inner">
                                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                                        <p class="product__price">8 900 ₽</p>
                                    </div>
                                </a>
                                <a href="#" class="swiper-slide product">
                                    <img src="assets/img/export/product1.webp" alt="T-SHIRT DENSE BLACK 100" class="product__img">
                                    <div class="product__inner">
                                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                                        <p class="product__price">8 900 ₽</p>
                                    </div>
                                </a>
                                <a href="#" class="swiper-slide product">
                                    <img src="assets/img/export/product1.webp" alt="T-SHIRT DENSE BLACK 100" class="product__img">
                                    <div class="product__inner">
                                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                                        <p class="product__price">8 900 ₽</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <a href="#">Смотреть весь результат ( 4 изделия )</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-3.7.1.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/hystmodal.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/swiper-bundle.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/mask.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/main.min.js');

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/include/footer/script.js');
?>
<script src="/include/footer/script.js"></script>
<button class="hystmodal__shadow"></button>
</body>
</html>