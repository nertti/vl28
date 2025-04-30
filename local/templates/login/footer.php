<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
/** @var bool $isMainPage */
?>
</main>
<footer class="footer footer_404 footer_dark">
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
            <svg width="176" height="46" viewBox="0 0 176 46" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo__img svg replaced-svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M131.088 2.53493C129.787 2.78435 129.252 2.96669 127.97 3.59692C126.731 4.20635 126.315 4.48139 125.241 5.40001C123.688 6.72878 122.15 9.17986 121.506 11.3514C121.263 12.1726 121.217 12.6958 121.214 14.6678C121.211 17.3509 121.382 18.1269 122.449 20.2543C123.891 23.1314 126.561 25.4235 129.59 26.3855C130.116 26.5528 130.566 26.7458 130.589 26.8144C130.612 26.883 128.742 28.8546 126.434 31.1958L122.238 35.4523V38.1783V40.9043H133.926H145.613V38.1498V35.3953H137.982C132.513 35.3953 130.351 35.3523 130.351 35.2434C130.351 35.1599 133.181 32.246 136.641 28.768C140.74 24.6478 143.151 22.1188 143.56 21.5099C144.351 20.3322 145.107 18.5351 145.393 17.1525C145.802 15.1678 145.637 12.4114 145.001 10.6185C143.637 6.77478 140.167 3.63466 136.23 2.68119C134.993 2.38178 132.288 2.30479 131.088 2.53493ZM158.389 2.54581C153.697 3.4395 149.976 7.45817 149.332 12.3265C148.931 15.3572 149.888 18.484 151.963 20.9249L152.899 22.0253L152.109 22.9165C150.561 24.6634 149.589 26.8054 149.311 29.0798C149.042 31.2851 149.624 34.0598 150.768 36.0226C152.277 38.6123 154.871 40.5727 157.77 41.3146C159.265 41.6972 161.918 41.702 163.391 41.3249C165.336 40.8266 166.998 39.9066 168.394 38.5562C172.145 34.9266 173.038 29.4061 170.584 25.015C170.325 24.5507 169.705 23.6926 169.207 23.1082C168.708 22.5237 168.301 22.0061 168.301 21.9581C168.301 21.9099 168.698 21.4412 169.183 20.9165C171.231 18.6998 172.264 15.4189 171.879 12.3442C171.504 9.34279 169.933 6.57136 167.603 4.79884C166.324 3.82568 164.865 3.0912 163.507 2.73669C162.115 2.37338 159.757 2.28537 158.389 2.54581ZM4.76749 3.12191C4.71703 3.17232 4.67578 11.694 4.67578 22.059V40.9043H7.41107C9.77841 40.9043 10.1576 40.875 10.2298 40.6864C10.2757 40.5666 10.3133 32.1193 10.3133 21.9146C10.3133 7.66737 10.2749 3.32216 10.1483 3.19532C9.97008 3.01683 4.94088 2.9481 4.76749 3.12191ZM14.7984 3.35811C14.6958 3.76742 14.7043 40.1611 14.8071 40.5944C14.8802 40.9029 14.8927 40.9043 17.6162 40.9043H20.3517L20.317 22.0016L20.282 3.09891L17.5821 3.06159L14.882 3.02426L14.7984 3.35811ZM35.155 3.12191C35.1045 3.17232 35.0633 11.694 35.0633 22.059V40.9043H37.8133C39.3258 40.9043 40.5692 40.8579 40.5766 40.801C40.5839 40.7441 40.6106 32.3012 40.6359 22.0387C40.6678 9.10535 40.6374 3.32602 40.537 3.20482C40.4294 3.07494 39.7309 3.03005 37.8193 3.03005C36.4043 3.03005 35.2053 3.07136 35.155 3.12191ZM75.7175 3.12191C75.667 3.17232 75.6258 11.6948 75.6258 22.0608V40.908L80.1758 40.8717L84.7258 40.8354L85.5504 39.3893C86.0039 38.594 86.7002 37.3854 87.0978 36.7037C87.4953 36.022 88.6727 33.9768 89.7143 32.1588C90.7557 30.3408 91.8402 28.4506 92.1243 27.9582C92.4082 27.4658 93.7396 25.1417 95.083 22.7935C96.4264 20.4453 97.6956 18.2452 97.9037 17.9043C98.1117 17.5634 98.8091 16.3549 99.4534 15.2187C100.098 14.0824 101.04 12.4401 101.547 11.569C102.055 10.6979 102.594 9.76822 102.746 9.5031C102.897 9.23798 103.756 7.7456 104.655 6.18669C105.553 4.62765 106.288 3.27974 106.288 3.19118C106.288 3.00884 100.42 2.95554 100.162 3.13541C100.082 3.19105 99.7682 3.67047 99.4643 4.20071C99.1606 4.73094 97.4978 7.61283 95.7693 10.6049C94.0406 13.597 92.0904 16.9747 91.4354 18.1109C90.7803 19.2471 89.9042 20.7655 89.4883 21.4851C85.5775 28.252 82.3285 33.9237 82.1115 34.3624C81.8092 34.974 81.3997 35.533 81.2539 35.533C81.1754 35.533 81.1258 29.2324 81.1258 19.2815V3.03005H78.4675C77.0053 3.03005 75.7678 3.07136 75.7175 3.12191ZM111.931 3.14353C111.8 3.18678 111.482 3.69429 110.318 5.71568C110.013 6.24591 109.457 7.20654 109.083 7.85041C107.276 10.9604 106.469 12.3591 103.127 18.1797C101.147 21.6263 99.1689 25.066 98.7298 25.8235C98.2906 26.5809 96.8726 29.0407 95.5785 31.2895L93.2258 35.3782V38.1413V40.9043H105.738H118.251V38.1498V35.3953H109.038C100.356 35.3953 99.8258 35.3814 99.8263 35.1543C99.8266 35.0217 99.9505 34.71 100.102 34.4617C100.454 33.8836 102.119 31.0049 103.785 28.0959C104.966 26.0327 107.538 21.5683 110.251 16.8714C111.136 15.34 113.373 11.4383 116.293 6.33544C116.705 5.61582 117.253 4.60865 117.511 4.09741L117.979 3.16777L117.049 3.16102C116.538 3.15717 115.207 3.14174 114.092 3.12659C112.978 3.1113 112.005 3.11902 111.931 3.14353ZM132.138 7.94461C131.612 8.03138 130.346 8.5506 129.732 8.93099C128.894 9.4509 127.714 10.7393 127.286 11.6019C126.285 13.6193 126.336 16.0244 127.419 17.9858C127.978 18.9966 129.311 20.2634 130.357 20.7775C133.636 22.3887 137.746 20.9888 139.334 17.7206C140.369 15.5902 140.369 13.7453 139.334 11.615C138.058 8.98911 135.065 7.46202 132.138 7.94461ZM159.475 7.97215C157.862 8.31426 156.109 9.71272 155.345 11.2668C154.969 12.0306 154.897 12.346 154.85 13.4344C154.808 14.3989 154.853 14.887 155.037 15.4454C155.567 17.0584 156.683 18.3142 158.246 19.056C159.088 19.4554 159.266 19.488 160.601 19.487C161.929 19.4859 162.117 19.4516 162.953 19.0571C164.512 18.3208 165.634 17.0591 166.165 15.4454C166.552 14.2693 166.421 12.4543 165.864 11.2628C164.933 9.27159 163.125 8.00962 161.066 7.91459C160.47 7.88718 159.754 7.91307 159.475 7.97215ZM159.338 24.5798C157.631 25.0507 155.973 26.4088 155.269 27.9143C154.929 28.6414 154.895 28.8626 154.895 30.3024C154.895 31.8209 154.914 31.9291 155.351 32.8175C156.2 34.5447 158.113 35.9381 159.913 36.1407C162.614 36.4444 165.279 34.6674 166.165 31.9723C166.35 31.4085 166.393 30.939 166.345 29.9753C166.269 28.4182 165.882 27.5055 164.805 26.3402C163.933 25.3973 163.051 24.8787 161.826 24.5896C160.779 24.3424 160.206 24.3402 159.338 24.5798Z" fill="#0E0E0E"></path>
            </svg>
        </a>
        <span>© VL28, 2024. Все права защищены</span>
    </div>
</footer>
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
    <span style="display: none" data-hystmodal="#thanksModal" id="openThanksModal"></span>
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
<?php
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-3.7.1.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/hystmodal.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/swiper-bundle.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/mask.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/main.min.js');
?>
<button class="hystmodal__shadow"></button>
</body>
</html>