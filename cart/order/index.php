<?php

/** @var \CMain $APPLICATION */
/** @var \CMain $USER */
/** @global  $userBonus */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");


Bitrix\Main\Loader::includeModule("Sale");
Bitrix\Main\Loader::includeModule("Catalog");

use Bitrix\Sale\Delivery\Services\Manager;

$deliveriesList = Manager::getActiveList();
array_shift($deliveriesList); // убираем бесплатную
//pr($deliveriesList, true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php");
$userBonus = str_replace(' ', '', $userBonus);
function calculateMaxPointsToSpend($total, $bonus)
{
    // Проверяем корректность входных данных
    if ($total <= 0 || $bonus < 0) {
        return 0;
    }

    // Вычисляем максимальную сумму в баллах
    $maxPoints = min($total, $bonus);

    return $maxPoints;
}

function getProductInfo($productId)
{
    $result = \CIBlockElement::GetList(
        array(),
        array(
            "ID" => $productId,
            "=ACTIVE" => "Y"
        ),
        false,
        false,
        array("*")
    );

    if ($item = $result->Fetch()) {
        return $item;
    }
    return false;
}

function getElementProperties($iblockId, $elementId)
{
    $db_props = CIBlockElement::GetProperty($iblockId, $elementId, "sort", "asc", array("CODE" => "SIZE"));
    if ($ar_props = $db_props->Fetch()) {
        return $ar_props['VALUE_ENUM'];
    }
}

?>

<?php /*
$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "order", Array(
	"ADDITIONAL_PICT_PROP_8" => "-",
		"ALLOW_AUTO_REGISTER" => "N",	// Оформлять заказ с автоматической регистрацией пользователя
		"ALLOW_NEW_PROFILE" => "Y",	// Разрешить множество профилей покупателей
		"ALLOW_USER_PROFILES" => "Y",	// Разрешить использование профилей покупателей
		"BASKET_IMAGES_SCALING" => "standard",	// Режим отображения изображений товаров
		"BASKET_POSITION" => "after",	// Расположение списка товаров
		"COMPATIBLE_MODE" => "Y",	// Режим совместимости для предыдущего шаблона
		"DELIVERIES_PER_PAGE" => "8",	// Количество доставок на странице
		"DELIVERY_FADE_EXTRA_SERVICES" => "Y",	// Дополнительные услуги, которые будут показаны в пройденном (свернутом) блоке
		"DELIVERY_NO_AJAX" => "Y",	// Когда рассчитывать доставки с внешними системами расчета
		"DELIVERY_NO_SESSION" => "Y",	// Проверять сессию при оформлении заказа
		"DELIVERY_TO_PAYSYSTEM" => "d2p",	// Последовательность оформления
		"DISABLE_BASKET_REDIRECT" => "N",	// Оставаться на странице оформления заказа, если список товаров пуст
		"MESS_DELIVERY_CALC_ERROR_TEXT" => "Вы можете продолжить оформление заказа, а чуть позже менеджер магазина  свяжется с вами и уточнит информацию по доставке.",	// Текст ошибки расчета доставки
		"MESS_DELIVERY_CALC_ERROR_TITLE" => "Не удалось рассчитать стоимость доставки.",	// Заголовок ошибки расчета доставки
		"MESS_FAIL_PRELOAD_TEXT" => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически. Обратите внимание на развернутый блок с информацией о заказе. Здесь вы можете внести необходимые изменения или оставить как есть и нажать кнопку \"#ORDER_BUTTON#\".",	// Текст уведомления о неудачной загрузке данных заказа
		"MESS_SUCCESS_PRELOAD_TEXT" => "Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически. Если все заполнено верно, нажмите кнопку \"#ORDER_BUTTON#\".",	// Текст уведомления о корректной загрузке данных заказа
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",	// Разрешить оплату с внутреннего счета только в полном объеме
		"PATH_TO_AUTH" => "/auth/",	// Путь к странице авторизации
		"PATH_TO_BASKET" => "basket.php",	// Путь к странице корзины
		"PATH_TO_PAYMENT" => "payment.php",	// Страница подключения платежной системы
		"PATH_TO_PERSONAL" => "index.php",	// Путь к странице персонального раздела
		"PAY_FROM_ACCOUNT" => "Y",	// Разрешить оплату с внутреннего счета
		"PAY_SYSTEMS_PER_PAGE" => "8",	// Количество платежных систем на странице
		"PICKUPS_PER_PAGE" => "5",	// Количество пунктов самовывоза на странице
		"PRODUCT_COLUMNS_HIDDEN" => array(	// Свойства товаров отображаемые в свернутом виде в списке товаров
			0 => "PROPERTY_MATERIAL",
		),
		"PRODUCT_COLUMNS_VISIBLE" => array(	// Выбранные колонки таблицы списка товаров
			0 => "PREVIEW_PICTURE",
			1 => "PROPS",
		),
		"PROPS_FADE_LIST_1" => array(
			0 => "17",
			1 => "19",
		),
		"SEND_NEW_USER_NOTIFY" => "Y",	// Отправлять пользователю письмо, что он зарегистрирован на сайте
		"SERVICES_IMAGES_SCALING" => "standard",	// Режим отображения вспомагательных изображений
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"SHOW_BASKET_HEADERS" => "N",	// Показывать заголовки колонок списка товаров
		"SHOW_COUPONS" => "Y",	// Отображать поля ввода купонов
		"SHOW_COUPONS_BASKET" => "Y",	// Показывать поле ввода купонов в блоке списка товаров
		"SHOW_COUPONS_DELIVERY" => "Y",	// Показывать поле ввода купонов в блоке доставки
		"SHOW_COUPONS_PAY_SYSTEM" => "Y",	// Показывать поле ввода купонов в блоке оплаты
		"SHOW_DELIVERY_INFO_NAME" => "Y",	// Отображать название в блоке информации по доставке
		"SHOW_DELIVERY_LIST_NAMES" => "Y",	// Отображать названия в списке доставок
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",	// Показывать название родительской доставки
		"SHOW_MAP_IN_PROPS" => "N",	// Показывать карту в блоке свойств заказа
		"SHOW_NEAREST_PICKUP" => "N",	// Показывать ближайшие пункты самовывоза
		"SHOW_NOT_CALCULATED_DELIVERIES" => "L",	// Отображение доставок с ошибками расчета
		"SHOW_ORDER_BUTTON" => "always",	// Отображать кнопку оформления заказа (для неавторизованных пользователей)
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",	// Отображать название в блоке информации по платежной системе
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",	// Отображать названия в списке платежных систем
		"SHOW_STORES_IMAGES" => "Y",	// Показывать изображения складов в окне выбора пункта выдачи
		"SHOW_TOTAL_ORDER_BUTTON" => "Y",	// Отображать дополнительную кнопку оформления заказа
		"SHOW_VAT_PRICE" => "Y",	// Отображать значение НДС
		"SKIP_USELESS_BLOCK" => "Y",	// Пропускать шаги, в которых один элемент для выбора
		"TEMPLATE_LOCATION" => "popup",	// Визуальный вид контрола выбора местоположений
		"TEMPLATE_THEME" => "blue",	// Цветовая тема
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",	// Заменить стандартные фразы на свои
		"USE_CUSTOM_ERROR_MESSAGES" => "Y",	// Заменить стандартные фразы на свои
		"USE_CUSTOM_MAIN_MESSAGES" => "N",	// Заменить стандартные фразы на свои
		"USE_PREPAYMENT" => "N",	// Использовать предавторизацию для оформления заказа (PayPal Express Checkout)
		"USE_YM_GOALS" => "N",	// Использовать цели счетчика Яндекс.Метрики
		"USER_CONSENT" => "Y",	// Запрашивать согласие
		"USER_CONSENT_ID" => "0",	// Соглашение
		"USER_CONSENT_IS_CHECKED" => "Y",	// Галка по умолчанию проставлена
		"USER_CONSENT_IS_LOADED" => "N",	// Загружать текст сразу
		"COMPONENT_TEMPLATE" => ".default",
		"ALLOW_APPEND_ORDER" => "Y",	// Разрешить оформлять заказ на существующего пользователя
		"SPOT_LOCATION_BY_GEOIP" => "Y",	// Определять местоположение покупателя по IP-адресу
		"USE_PRELOAD" => "Y",	// Автозаполнение оплаты и доставки по предыдущему заказу
		"SHOW_PICKUP_MAP" => "Y",	// Показывать карту для доставок с самовывозом
		"PICKUP_MAP_TYPE" => "yandex",	// Тип используемых карт
		"ACTION_VARIABLE" => "soa-action",	// Название переменной, в которой передается действие
		"EMPTY_BASKET_HINT_PATH" => "/",	// Путь к странице для продолжения покупок
		"USE_PHONE_NORMALIZATION" => "Y",	// Использовать нормализацию номера телефона
		"ADDITIONAL_PICT_PROP_2" => "-",	// Дополнительная картинка [Каталог]
		"ADDITIONAL_PICT_PROP_5" => "-",	// Дополнительная картинка [Торговые предложения]
		"HIDE_ORDER_DESCRIPTION" => "N",	// Скрыть поле комментариев к заказу
		"USE_ENHANCED_ECOMMERCE" => "N",	// Отправлять данные электронной торговли в Google и Яндекс
		"MESS_PAY_SYSTEM_PAYABLE_ERROR" => "Вы сможете оплатить заказ после того, как менеджер проверит наличие полного комплекта товаров на складе. Сразу после проверки вы получите письмо с инструкциями по оплате. Оплатить заказ можно будет в персональном разделе сайта.",	// Текст уведомления при статусе заказа, недоступном для оплаты
	),
	false
);
 */
?>

<?php
$fUserId = Bitrix\Sale\Fuser::getId();
$siteId = Bitrix\Main\Context::getCurrent()->getSite();
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

if (empty($basket->getQuantityList())) {
    header('Location: /catalog/');
}

$basketItems = $basket->getBasketItems(); // массив объектов Sale\BasketItem
foreach ($basket as $basketItem) {
    //echo $basketItem->getField('NAME') . $basketItem->getField('PRODUCT_ID') . ' - ' . $basketItem->getQuantity() . '<br />';
}

// узнаём есть ли у него система лояльности и скидочная карта
$fullPrice = $basket->getBasePrice();
$salePrice = 0;
if ($USER->isAuthorized()) {
    $userId = $USER->GetID();
    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();
    if ($arUser['UF_BID'] == '1') {
        if ($arUser['UF_CARD'] == 10) {
            $fullPrice = floor($fullPrice * 0.95);
            $salePrice = floor($fullPrice * 0.05);
        } elseif ($arUser['UF_CARD'] == 11) {
            $fullPrice = floor($fullPrice * 0.90);
            $salePrice = floor($fullPrice * 0.10);
        } elseif ($arUser['UF_CARD'] == 12) {
            $fullPrice = floor($fullPrice * 0.85);
            $salePrice = floor($fullPrice * 0.15);
        }
    }
}
?>
<section class="checkout first-section">
    <div class="container">
        <p class="h2">Оформление заказа</p>
        <div class="checkout__inner">
            <div class="checkout__cart">
                <?php foreach ($basket as $basketItem): ?>
                    <div class="checkout__cart-item" id="<?= $basketItem->getField('ID') ?>">
                        <?php
                        $product = getProductInfo($basketItem->getField('PRODUCT_ID'));
                        $propertySize = getElementProperties($product['IBLOCK_ID'], $product['ID']);
                        //pr($product, true);
                        ?>
                        <img src="<?= CFile::getPath($product['PREVIEW_PICTURE']) ?>" alt="<?= $product['NAME'] ?>">
                        <p class="checkout__cart-title"><?= $product['NAME'] ?></p>
                        <div class="checkout__cart-color" style="display:none;">
                            <span style="background: #000;"></span>
                        </div>
                        <p class="checkout__cart-value"><?= $propertySize ?></p>
                        <div class="checkout__cart-quantity">
                            <div class="plus"></div>
                            <input type="number" min="1" max="30" class="checkout__cart-input countProduct"
                                   value="<?= $basketItem->getQuantity() ?>">
                            <div class="minus"></div>
                        </div>
                        <p class="checkout__cart-price"><?= $basketItem->getFinalPrice() ?> ₽</p>
                        <span class="checkout__cart-remove pointer"></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <form id="form" action="/ajax/createOrder.php" class="checkout__form">
                <input id="siteId" type="hidden" name="siteId" value="<?= $siteId ?>">
                <input id="fUserId" type="hidden" name="fUserId" value="<?= $fUserId ?>">
                <div class="checkout__form-left">
                    <div class="checkout__label">
                        <p class="checkout__name">E-mail</p>
                        <div class="checkout__inputs">
                            <p class="error-text email" style="display: none;">Пожалуйста, введите свой email.</p>
                            <input type="text" class="form-input checkout__input email" placeholder="E-mail"
                                   name="email">
                            <label class="checkout__checkbox">
                                <input type="checkbox" name="news">
                                <div class="checkmark"></div>
                                <span>
                                  Хочу получать новости и узнавать о специальных предложениях в числе первых
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Имя</p>
                        <div class="checkout__inputs">
                            <p class="error-text name" style="display: none;">Пожалуйста, введите своё имя.</p>
                            <input type="text" name="name" class="form-input checkout__input name" placeholder="Имя">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Фамилия</p>
                        <div class="checkout__inputs">
                            <p class="error-text surname" style="display: none;">Пожалуйста, введите свою фамилию.</p>
                            <input type="text" name="surname" class="form-input checkout__input surname"
                                   placeholder="Фамилия">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Телефон</p>
                        <div class="checkout__inputs">
                            <p class="error-text phone" style="display: none;">Пожалуйста, введите свой телефон.</p>
                            <input type="text" name="phone" class="form-input phone-input checkout__input phone"
                                   placeholder="+7 (___) ___-__-__">
                        </div>
                    </div>
                    <div class="checkout__label checkout__label_radios">
                        <p class="checkout__name">Способ доставки</p>
                        <div class="checkout__inputs">
                            <p class="error-text delivery" style="display: none;">Пожалуйста, выберите способ
                                доставки.</p>
                            <?php foreach ($deliveriesList as $delivery): ?>
                                <label class="checkout__radio export">
                                    <input type="radio" name="delivery" value="<?= $delivery['ID'] ?>">
                                    <div class="checkmark"></div>
                                    <span>
                                        <?= $delivery['NAME'] ?>
                                        <span><?= $delivery['DESCRIPTION'] ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="address" style="flex-direction: column">
                        <div class="checkout__label" style="padding-bottom: 20px">
                            <p class="checkout__name">Населённый пункт</p>
                            <div class="checkout__inputs">
                                <p class="error-text city" style="display: none;">Пожалуйста, введите свой город.</p>
                                <input id="city" type="text" name="city" class="form-input checkout__input city"
                                       placeholder="Населённый пункт">
                            </div>
                        </div>
                        <div class="checkout__label address_more">
                            <p class="checkout__name">Улица</p>
                            <div class="checkout__inputs">
                                <p class="error-text street" style="display: none;">Пожалуйста, введите свою улицу.</p>
                                <input type="text" name="street" class="form-input checkout__input street"
                                       placeholder="пр-кт Ленинградский">
                                <div class="checkout__inputs-inner">
                                    <div class="checkout__inputs-item">
                                        <p class="checkout__name">Дом</p>
                                        <p class="error-text dom" style="display: none;">Пожалуйста, введите свой
                                            дом.</p>
                                        <input type="text" name="dom" class="form-input checkout__input dom"
                                               placeholder="14 Б">
                                    </div>
                                    <div class="checkout__inputs-item">
                                        <p class="checkout__name">Квартира / офис</p>
                                        <p class="error-text kvartira" style="display: none;">Пожалуйста, введите свою
                                            квартиру.</p>
                                        <input type="text" name="kvartira" class="form-input checkout__input kvartira"
                                               placeholder="79">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout__label checkout__label_radios">
                        <p class="checkout__name">Способ оплаты</p>
                        <div class="checkout__inputs">
                            <label class="checkout__radio" id="otherCity">
                                <input type="radio" name="payment" value="card" checked="">
                                <div class="checkmark"></div>
                                <span>
                      Оплата картой онлайн
                    </span>
                            </label>
                            <label class="checkout__radio" id="moskva">
                                <input type="radio" name="payment" value="card_moskoy">
                                <div class="checkmark"></div>
                                <span>
                      Картой при получении (для Москвы)
                    </span>
                            </label>
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Комментарий</p>
                        <div class="checkout__inputs">
                            <textarea name="comment" class="form-input checkout__textarea"
                                      placeholder="Домофон не работает"></textarea>
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Промокод</p>
                        <div class="checkout__inputs">
                            <input type="text" name="promo" class="form-input checkout__input">
                        </div>
                    </div>
                </div>
                <div class="checkout__form-right">

                    <div class="checkout__links">
                        <a href="/cart/" class="checkout__back">Назад</a>
                        <?php if (!$USER->isAuthorized()): ?>
                            <a href="/login/" class="checkout__login">Войти в личный кабинет</a>
                        <?php endif; ?>
                    </div>

                    <div class="checkout__param">
                        <div class="checkout__param-item">
                            <p>Доставка:</p>
                            <p>0 ₽</p>
                        </div>
                        <div class="checkout__param-item">
                            <p>Скидка по промокоду:</p>
                            <p>0 ₽</p>
                        </div>

                        <!-- Если юзер имеет скидку -->
                        <?php if ($salePrice > 0): ?>
                            <div class="checkout__param-item checkout__param-item_sale">
                                <p>Скидка по программе лояльности</p>
                                <p id="salePrice">-<?= $salePrice ?> ₽</p>
                            </div>
                        <?php endif; ?>

                        <!-- Если юзер состоит в программе лояльности -->
                        <div class="checkout__param-item" <?php if ($arUser['UF_BID'] !== '1'): ?>
                            style="display: none" <?php endif; ?>>
                            <p>Баллов начислится:</p>
                            <p class="bonusPoints"></p>
                            <input class="bonusPointsValue" type="hidden" name="bonusPoints" value="">
                        </div>
                        <div class="checkout__param-item totalPrice">
                            <p>Итого:</p>
                            <strong><?= $fullPrice; ?> ₽</strong>
                        </div>
                    </div>
                    <!-- Если юзер авторизован -->
                    <div class="promo" <?php if ($arUser['UF_BID'] !== '1'): ?> style="display: none" <?php endif; ?>>
                        <!-- Если юзер тратит баллы -->
                        <div id="applyBonusBlock" class="checkout__param-item checkout__param-item_sale"
                             style="display: none">
                            <p>Программа лояльности</p>
                            <p id="applyBonusText">-0 ₽</p>
                        </div>
                        <?php if ($arUser['UF_BID'] !== '1' && $userBonus > 0): ?>
                            <div class="promo__activate">
                                <p>Программа лояльности: <strong><?= $userBonus ?> баллов</strong></p>
                                <div class="promo__btn" <?php if ($userBonus <= 0): ?> style="display: none" <?php endif; ?>>
                                    <div class="promo__btn-circle"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="promo__show" style="display: none;">
                            <div class="promo__form">
                                <input type="hidden" name="setBonus" id="setBonus" value="N">
                                <input type="number" class="promo__input" name="bonus" id="bonus"
                                       max="<?= calculateMaxPointsToSpend($fullPrice, $userBonus) ?>"
                                       value="<?= calculateMaxPointsToSpend($fullPrice, $userBonus) ?>">
                                <button id="applyBonus" type="button" class="border-btn">Применить</button>
                            </div>
                        </div>
                    </div>

                    <button id="saveBtn" type="submit" class="black-btn">Оформить заказ</button>
                    <p class="checkout__small">
                        Нажимая на&nbsp;кнопку «Оформить заказ», я&nbsp;принимаю условия&nbsp;<a href="/oferta/">публичной
                            оферты</a>&nbsp;и&nbsp;<a href="/personal/">политики конфиденциальности</a>
                    </p>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const myModalSuccessOrder = new HystModal({
                            linkAttributeName: 'data-hystmodal',
                            afterClose: function (modal) {
                                window.location = '/'
                            },
                        });
                        const myModalReject = new HystModal({
                            linkAttributeName: 'data-hystmodal',
                        });

                        const form = document.querySelector('#form');
                        const saveBtn = document.querySelector('#saveBtn');
                        // Если форма найдена, добавляем слушатель события submit
                        if (form) {
                            form.addEventListener('submit', handleFormSubmit);
                        } else {
                            console.warn('Форма не найдена на странице');
                        }

                        function handleFormSubmit(event) {
                            event.preventDefault();
                            saveBtn.innerHTML = `
                              <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                              <span role='status'>Оформляем заказ...</span>
                            `;
                            const formData = new FormData(form);
                            fetch(form.action, {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    saveBtn.innerHTML = `Оформить заказ`;

                                    let errorEmailError = document.querySelector('.error-text.email');
                                    let errorEmailInput = document.querySelector('.form-input.email');

                                    let errorNameError = document.querySelector('.error-text.name');
                                    let errorNameInput = document.querySelector('.form-input.name');

                                    let errorSurnameError = document.querySelector('.error-text.surname');
                                    let errorSurnameInput = document.querySelector('.form-input.surname');

                                    let errorPhoneError = document.querySelector('.error-text.phone');
                                    let errorPhoneInput = document.querySelector('.form-input.phone');

                                    let errorDeliveryError = document.querySelector('.error-text.delivery');

                                    let errorCityError = document.querySelector('.error-text.city');
                                    let errorCityInput = document.querySelector('.form-input.city');

                                    let errorStreetError = document.querySelector('.error-text.street');
                                    let errorStreetInput = document.querySelector('.form-input.street');

                                    let errorDomError = document.querySelector('.error-text.dom');
                                    let errorDomInput = document.querySelector('.form-input.dom');

                                    let errorKvartiraError = document.querySelector('.error-text.kvartira');
                                    let errorKvartiraInput = document.querySelector('.form-input.kvartira');

                                    if (data.status === 'error') {
                                        // Сначала убираем все предыдущие ошибки
                                        errorEmailInput.classList.remove('error');
                                        errorEmailError.style.display = 'none';

                                        errorNameInput.classList.remove('error');
                                        errorNameError.style.display = 'none';

                                        errorSurnameInput.classList.remove('error');
                                        errorSurnameError.style.display = 'none';

                                        errorPhoneInput.classList.remove('error');
                                        errorPhoneError.style.display = 'none';

                                        errorDeliveryError.style.display = 'none';

                                        errorCityInput.classList.remove('error');
                                        errorCityError.style.display = 'none';

                                        errorStreetInput.classList.remove('error');
                                        errorStreetError.style.display = 'none';

                                        errorDomInput.classList.remove('error');
                                        errorDomError.style.display = 'none';

                                        errorKvartiraInput.classList.remove('error');
                                        errorKvartiraError.style.display = 'none';

                                        if (data.message.email) {
                                            errorEmailInput.classList.add('error');
                                            errorEmailError.style.display = 'block';
                                            errorEmailError.innerHTML = data.message.email;
                                        }

                                        if (data.message.name) {
                                            errorNameInput.classList.add('error');
                                            errorNameError.style.display = 'block';
                                            errorNameError.innerHTML = data.message.name;
                                        }

                                        if (data.message.surname) {
                                            errorSurnameInput.classList.add('error');
                                            errorSurnameError.style.display = 'block';
                                            errorSurnameError.innerHTML = data.message.surname;
                                        }

                                        if (data.message.phone) {
                                            errorPhoneInput.classList.add('error');
                                            errorPhoneError.style.display = 'block';
                                            errorPhoneError.innerHTML = data.message.phone;
                                        }

                                        if (data.message.delivery) {
                                            errorDeliveryError.style.display = 'block';
                                            errorDeliveryError.innerHTML = data.message.delivery;
                                        }

                                        if (data.message.city) {
                                            errorCityInput.classList.add('error');
                                            errorCityError.style.display = 'block';
                                            errorCityError.innerHTML = data.message.city;
                                        }

                                        if (data.message.street) {
                                            errorStreetInput.classList.add('error');
                                            errorStreetError.style.display = 'block';
                                            errorStreetError.innerHTML = data.message.street;
                                        }

                                        if (data.message.dom) {
                                            errorDomInput.classList.add('error');
                                            errorDomError.style.display = 'block';
                                            errorDomError.innerHTML = data.message.dom;
                                        }

                                        if (data.message.kvartira) {
                                            errorKvartiraInput.classList.add('error');
                                            errorKvartiraError.style.display = 'block';
                                            errorKvartiraError.innerHTML = data.message.kvartira;
                                        }

                                        window.scrollTo({
                                            top: document.querySelector('.checkout__form').offsetTop - 100,
                                            behavior: 'smooth'
                                        });
                                    } else {
                                        document.querySelector('#alertModal .alertText .h2').textContent = data.message
                                        if (data.pay_url != '') {
                                            document.querySelector('#alertModal .alertText .text').textContent = 'Перейти к оплате'
                                            document.querySelector('#alertModal .alertText .text').href = data.pay_url
                                        }
                                        myModalSuccessOrder.open('#alertModal');
                                    }
                                })
                                .catch(error => {
                                    console.error('Ошибка при отправке формы:', error);
                                });
                        }
                    });
                    document.addEventListener('DOMContentLoaded', function () {

                        const inputBonus = document.getElementById('bonus');
                        // Обработчик изменения значения
                        inputBonus.addEventListener('change', function () {
                            let value = Number(this.value);

                            // Если значение больше максимального, устанавливаем максимальное
                            if (value > this.max) {
                                value = Number(this.max);
                                this.value = value - value * 0.10;
                            }
                        });
                        inputBonus.addEventListener('input', function () {
                            let value = Number(this.value);

                            // Если значение больше максимального, устанавливаем максимальное
                            if (value > this.max) {
                                value = Number(this.max);
                                this.value = value - value * 0.10;
                            }
                        });

                        const applyBonusBtn = document.querySelector('#applyBonus');
                        if (applyBonusBtn) {
                            applyBonusBtn.addEventListener('click', handleFormSubmit);
                        } else {
                            console.warn('Форма не найдена на странице');
                        }

                        function handleFormSubmit(event) {
                            event.preventDefault();
                            applyBonusBtn.innerHTML = `
                              <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                              <span role='status'>Списываем бонусы</span>
                            `;
                            applyBonusBtn.disabled = true;
                            let totalPrice = document.querySelector('.totalPrice strong').textContent;
                            totalPrice = totalPrice.replace(/[^\d]/g, '')
                            const bonus = document.querySelector('#bonus').value;
                            document.querySelector('#setBonus').value = 'Y';
                            document.querySelector('.totalPrice strong').textContent = totalPrice - bonus + ' ₽';
                            document.querySelector('#applyBonusBlock').style.display = 'flex';
                            document.querySelector('#applyBonusText').textContent = '-' + bonus + ' ₽';
                        }
                    });
                </script>
            </form>
        </div>
</section>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/include/profile/sale.php"); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function calculateMaxPointsToSpend(total, bonus) {
            // Проверяем корректность входных данных
            if (total <= 0 || bonus < 0) {
                return 0;
            }

            // Возвращаем минимальное из двух значений
            return Math.min(total, bonus);
        }

        function calculateBonusPoints(total) {
            // Проверяем корректность входных данных
            if (total <= 0) {
                return 0;
            }
            let uf_card = <?=$discountPercent ?? 0?>;
            if (uf_card === 5) {
                // Вычисляем 5% от суммы заказа
                return Math.round(total * 0.05);
            } else if (uf_card === 10) {
                // Вычисляем 10% от суммы заказа
                return Math.round(total * 0.10);
            } else if (uf_card === 15) {
                // Вычисляем 15% от суммы заказа
                return Math.round(total * 0.15);
            } else {
                return 0;
            }
        }

        let totalPrice = <?=$fullPrice?>;
        document.querySelector('.bonusPoints').textContent = '+' + calculateBonusPoints(totalPrice) + ' баллов';
        document.querySelector('.bonusPointsValue').value = calculateBonusPoints(totalPrice);

        const countAria = document.querySelectorAll('.checkout__cart-quantity');
        countAria.forEach(element => {
            element.addEventListener('click', handleCountEdit);
        });
        const deleteBtn = document.querySelectorAll('.checkout__cart-remove');
        deleteBtn.forEach(element => {
            element.addEventListener('click', handleDelete);
        });

        // Изменение количества товаров в корзине
        function handleCountEdit(event) {
            setTimeout(() => {
                const wrapperProduct = event.target.closest('.checkout__cart-item');
                const countProduct = wrapperProduct.querySelector('.countProduct').value;
                const siteId = '<?=$siteId?>';
                const fUserId = '<?=$fUserId?>';
                fetch('/ajax/orderProduct.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: wrapperProduct.id,    // ID товара в корзине
                        count: countProduct,  // новое количество товара
                        siteId: siteId,
                        fUserId: fUserId,
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {

                        } else {
                            const total = data.totalPrice;
                            const bonus = <?=$userBonus ?? 0?>;
                            document.querySelector('.promo__input').value = calculateMaxPointsToSpend(total, bonus);
                            document.querySelector('.bonusPoints').textContent = '+' + calculateBonusPoints(total) + ' баллов';
                            document.querySelector('.bonusPointsValue').value = calculateBonusPoints(total);
                            if (data.price !== '') {
                                wrapperProduct.querySelector('.checkout__cart-price').textContent = data.price + ' ₽';
                            }
                            if (data.totalPrice !== '' && data.totalPrice !== 0) {
                                document.querySelector('.totalPrice strong').textContent = data.totalPrice + ' ₽';
                            }
                            if (data.salePrice !== '' && data.salePrice !== 0) {
                                document.querySelector('#salePrice').textContent = '-' + data.salePrice + ' ₽';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при авторизации:', error);
                    });
            }, 1);
        }

        // Удаление товаров в корзине
        function handleDelete(event) {
            setTimeout(() => {
                const wrapperProduct = event.target.closest('.checkout__cart-item');
                const siteId = '<?=$siteId?>';
                const fUserId = '<?=$fUserId?>';
                fetch('/ajax/orderProductDelete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: wrapperProduct.id,    // ID товара в корзине
                        siteId: siteId,
                        fUserId: fUserId,
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {

                        } else {
                            const total = data.totalPrice;
                            const bonus = <?=$userBonus ?? 0?>;
                            document.querySelector('.promo__input').value = calculateMaxPointsToSpend(total, bonus);
                            document.querySelector('.bonusPoints').textContent = '+' + calculateBonusPoints(total) + ' баллов';
                            document.querySelector('.bonusPointsValue').value = calculateBonusPoints(total);

                            wrapperProduct.style.display = 'none';
                            if (data.totalPrice !== '') {
                                document.querySelector('.totalPrice strong').textContent = data.totalPrice + ' ₽';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при авторизации:', error);
                    });
            }, 1);
        }
    });
</script>
<script>
    // поведение элементов на странице
    document.addEventListener('DOMContentLoaded', function () {
        // в зависимости от города скрываем способ оплаты
        const inputCity = document.querySelector('#city');

        function updateMoskvaInput(value) {
            if (value.toLowerCase() !== 'москва') {
                document.querySelector('#moskva').style.color = 'grey';
                document.querySelector('#moskva .checkmark').style.border = '1px solid grey';
                document.querySelector('#moskva input').disabled = true;
                document.querySelector('#otherCity input').checked = true;
            } else {
                document.querySelector('#moskva').style.color = 'black';
                document.querySelector('#moskva .checkmark').style.border = '1px solid black';
                document.querySelector('#moskva input').disabled = false;
            }
        }

        // Выполнение при загрузке
        updateMoskvaInput(inputCity.value);

        // Существующий обработчик события
        inputCity.addEventListener('input', function (e) {
            updateMoskvaInput(e.target.value);
        });

        // в зависимости от выбранной доставки выводим блоки (Адрес или выбор ПВЗ города)
        const selectExport = document.querySelectorAll('input[name="delivery"]');
        const streetBlock = document.querySelector('.address');
        const address_more = document.querySelector('.address_more');
        const city = document.querySelector('#city');

        function updateBlockVisibility() {
            const checkedValues = Array.from(selectExport)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            const shouldBeVisible =
                checkedValues.includes('137') ||
                checkedValues.includes('139')
            const shouldBeMoskow = checkedValues.includes('135')
            address_more.style.display = shouldBeVisible ? 'none' : 'flex';// если нужно скрыть, то указать none
            if (shouldBeMoskow) {
                city.value = 'Москва'
                city.disabled = true;
                updateMoskvaInput(city.value)
            } else {
                city.disabled = false;
                updateMoskvaInput(city.value)
            }
        }

        selectExport.forEach(checkbox => {
            checkbox.addEventListener('change', updateBlockVisibility);
        });

        // Промокод...
    });
</script>
<div class="hystmodal" id="alertModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="  min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2"></p>
                <div class="info" style="display: flex; align-items: center">
                    <div class="cart-icon-container">
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 50 50"
                                width="20px"
                                height="20px"
                                class="cart-icon"
                        >
                            <path d="M 25 2 C 12.309295 2 2 12.309295 2 25 C 2 37.690705 12.309295 48 25 48 C 37.690705 48 48 37.690705 48 25 C 48 12.309295 37.690705 2 25 2 z M 25 4 C 36.609824 4 46 13.390176 46 25 C 46 36.609824 36.609824 46 25 46 C 13.390176 46 4 36.609824 4 25 C 4 13.390176 13.390176 4 25 4 z M 25 11 A 3 3 0 0 0 22 14 A 3 3 0 0 0 25 17 A 3 3 0 0 0 28 14 A 3 3 0 0 0 25 11 z M 21 21 L 21 23 L 22 23 L 23 23 L 23 36 L 22 36 L 21 36 L 21 38 L 22 38 L 23 38 L 27 38 L 28 38 L 29 38 L 29 36 L 28 36 L 27 36 L 27 21 L 26 21 L 22 21 L 21 21 z"/>
                        </svg>

                        <div class="tooltip">
                        <span class="tooltip-text">
                           Вы сможете оплатить заказ позже. Ваш заказ будет отображаться в Личном кабинете в разделе «Заказы».
                        </span>
                        </div>
                    </div>
                    <a href="" class="text link" style="margin-left: 10px"></a>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
