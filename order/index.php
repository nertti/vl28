<?php

/** @var \CMain $APPLICATION */
/** @var \CMain $USER */
/** @global  $userBonus */
/** @global  $discountCard */
/** @global  $discountPercent */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
$userId = $USER->GetID();
$dbUser = CUser::GetList(array(), array(), ["ID" => $userId], ["SELECT" => ["*"]]);
$arUser = $dbUser->Fetch();

Bitrix\Main\Loader::includeModule("Sale");
Bitrix\Main\Loader::includeModule("Catalog");

use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Order;
use Bitrix\Currency\CurrencyManager;

$deliveriesList = Manager::getActiveList();
array_shift($deliveriesList); // убираем бесплатную
//pr($deliveriesList, true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/profile/sale.php");

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

<?php
$fUserId = Bitrix\Sale\Fuser::getId();
$siteId = Bitrix\Main\Context::getCurrent()->getSite();
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

// создаём временный заказ
$order = Order::create($siteId, $fUserId);
$order->setPersonTypeId(1); // проверь ID

$order->setBasket($basket);
$order->setField('CURRENCY', CurrencyManager::getBaseCurrency());

// 🔥 ВАЖНО — применяем скидки
$order->doFinalAction(true);

if (empty($basket->getQuantityList())) {
    header('Location: /catalog/');
}
$fullPrice = $basket->getPrice();
$salePrice = 0;
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
                            <div class="minus"></div>
                            <input type="number" min="1" max="30" class="checkout__cart-input countProduct"
                                   value="<?= $basketItem->getQuantity() ?>">
                            <div class="plus"></div>

                        </div>
                        <p class="checkout__cart-price"><?= number_format($basketItem->getFinalPrice(), 0, '', ' ') ?>
                            ₽</p>
                        <span class="checkout__cart-remove pointer"></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <form id="form" action="/ajax/createOrder.php" class="checkout__form">
                <input type="hidden" name="utmSource" value="<?=$arUser['UF_UTM_SOURCE']?>">
                <input type="hidden" name="utmCampaign" value="<?=$arUser['UF_UTM_CAMPAIGN']?>">
                <input type="hidden" name="utmPartner" value="<?=$arUser['UF_UTM_PARTNER']?>">

                <input id="siteId" type="hidden" name="siteId" value="<?= $siteId ?>">
                <input id="fUserId" type="hidden" name="fUserId" value="<?= $fUserId ?>">
                <input id="discountCard" type="hidden" name="discountCard" value="<?= $discountCard ?>">
                <input id="discountPercent" type="hidden" name="discountPercent" value="<?= $discountPercent ?>">
                <div class="checkout__form-left">
                    <div class="checkout__label">
                        <p class="checkout__name">E-mail</p>
                        <div class="checkout__inputs">
                            <p class="error-text email" style="display: none;">Пожалуйста, введите свой email.</p>
                            <input type="text" class="form-input checkout__input email" placeholder="E-mail"
                                   name="email" value="<?= $arUser['EMAIL'] ?>">
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
                            <input type="text" name="name" class="form-input checkout__input name" placeholder="Имя"
                                   value="<?= $arUser['NAME'] ?>">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Фамилия</p>
                        <div class="checkout__inputs">
                            <p class="error-text surname" style="display: none;">Пожалуйста, введите свою фамилию.</p>
                            <input type="text" name="surname" class="form-input checkout__input surname"
                                   placeholder="Фамилия" value="<?= $arUser['LAST_NAME'] ?>">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Телефон</p>
                        <div class="checkout__inputs">
                            <p class="error-text phone" style="display: none;">Пожалуйста, введите свой телефон.</p>
                            <input type="text" name="phone" class="form-input phone-input checkout__input phone"
                                   placeholder="+7 (___) ___-__-__" value="<?= $arUser['PERSONAL_PHONE'] ?>">
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
                    <div class="address address_more" style="flex-direction: column">
                        <div class="checkout__label" style="padding-bottom: 20px">
                            <p class="checkout__name">Населённый пункт</p>
                            <div class="checkout__inputs">
                                <p class="error-text city" style="display: none;">Пожалуйста, введите свой город.</p>
                                <input id="city" type="text" name="city" class="form-input checkout__input city">
                            </div>
                        </div>
                        <div class="checkout__label ">
                            <p class="checkout__name">Улица</p>
                            <div class="checkout__inputs">
                                <p class="error-text street" style="display: none;">Пожалуйста, введите свою улицу.</p>
                                <input type="text" name="street" class="form-input checkout__input street"
                                       placeholder="пр-кт">
                                <div class="checkout__inputs-inner">
                                    <div class="checkout__inputs-item">
                                        <p class="checkout__name">Дом</p>
                                        <p class="error-text dom" style="display: none;">Пожалуйста, введите свой
                                            дом.</p>
                                        <input type="text" name="dom" class="form-input checkout__input dom"
                                               placeholder="0">
                                    </div>
                                    <div class="checkout__inputs-item">
                                        <p class="checkout__name">Квартира / офис</p>
                                        <p class="error-text kvartira" style="display: none;">Пожалуйста, введите свою
                                            квартиру.</p>
                                        <input type="text" name="kvartira" class="form-input checkout__input kvartira"
                                               placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--сдек-->
                    <div id="cdek-map" style="max-width:1000px;width: 100%;height:600px;display: none"></div>
                    <!--сдек-->

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
                            <label style="display: none" class="checkout__radio" id="moskva">
                                <input type="radio" name="payment" value="card_moskoy">
                                <div class="checkmark"></div>
                                <span>
                                  Картой при получении (курьером по Москве)
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
                            <input type="text" name="promo" class="form-input checkout__input" style="margin-bottom: 10px">
                        </div>
                    </div>
                </div>
                <div class="checkout__form-right">

                    <div class="checkout__links">
                        <a href="/basket/" class="checkout__back">Назад</a>
                        <?php if (!$USER->isAuthorized()): ?>
                            <a href="/signin/" class="checkout__login">Войти в личный кабинет</a>
                        <?php endif; ?>
                    </div>

                    <div class="checkout__param">
                        <div class="checkout__param-item">
                            <p>Доставка:</p>
                            <p id="cdek-price">0 ₽</p>
                        </div>
<!--                        <div class="checkout__param-item">-->
<!--                            <p>Скидка по промокоду:</p>-->
<!--                            <p>0 ₽</p>-->
<!--                        </div>-->
                        <?php if ($USER->isAuthorized()): ?>
                            <!-- Если юзер авторизован -->
                            <div class="checkout__param-item">
                                <p>Баллов начислится:</p>
                                <p class="bonusPoints"></p>
                                <input class="bonusPointsValue" type="hidden" name="bonusPoints" value="">
                            </div>
                        <?php endif; ?>
                        <div class="checkout__param-item totalPrice">
                            <p>Итого:</p>
                            <strong><?= number_format($fullPrice, 0, '', ' '); ?> ₽</strong>
                            <input class="totalPriceValue" type="hidden" name="totalPrice" value="<?= number_format($fullPrice, 0, '', ' '); ?>">
                        </div>
                    </div>
                    <?php if ($USER->isAuthorized()): ?>
                        <!-- Если юзер авторизован -->
                        <div class="promo">
                            <!-- Если юзер тратит баллы -->
                            <div id="applyBonusBlock" class="checkout__param-item checkout__param-item_sale"
                                 style="display: none">
                                <p>Программа лояльности</p>
                                <p id="applyBonusText">-0 ₽</p>
                            </div>
                            <?php if ($userBonus > 0): ?>
                                <div class="promo__activate">
                                    <p>Программа лояльности: <strong><?= $userBonus ?> баллов</strong></p>
                                    <div class="promo__btn">
                                        <div class="promo__btn-circle"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="promo__show">
                                <div class="promo__form">
                                    <input type="hidden" name="setBonus" id="setBonus" value="N">
                                    <input type="number" class="promo__input" name="bonus" id="bonus"
                                           max="<?= calculateMaxPointsToSpend($fullPrice, $userBonus) ?>"
                                           value="0">
                                    <button id="applyBonus" type="button" class="border-btn">Применить</button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <button id="saveBtn" type="submit" class="black-btn">Оплатить заказ</button>
                    <p class="checkout__small">
                        Нажимая на&nbsp;кнопку «Оформить заказ», я&nbsp;принимаю условия&nbsp;<a href="/oferta/">публичной
                            оферты</a>&nbsp;и&nbsp;<a href="/personal/">политики конфиденциальности</a>
                    </p>
                </div>
                <input type="hidden" name="delivery_price" id="delivery-price">


                <input type="hidden" name="cdek" id="cdek" value="N">
                <input type="hidden" name="city_cdek" id="city_cdek" value="">
                <input type="hidden" name="city_code_cdek" id="city_code_cdek" value="">
                <input type="hidden" name="tariff_cdek" id="tariff_cdek" value="">
                <input type="hidden" name="address_cdek" id="address_cdek" value="">
                <input type="hidden" name="pvz_code_cdek" id="pvz_code_cdek" value="">
                <input type="hidden" name="postal_code_cdek" id="postal_code_cdek" value="">
                <input type="hidden" name="formatted_cdek" id="formatted_cdek" value="">
                <script>
                    /* оформление заказа */
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
                            const formData = new FormData(form);
                            const payment = formData.get('payment');
                            if (payment === 'card') {
                                saveBtn.innerHTML = `
                              <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                              <span role='status'>Нужно оплатить заказ...</span>
                            `;
                            } else {
                                saveBtn.innerHTML = `
                              <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                              <span role='status'>Оформляем заказ...</span>
                            `;
                            }

                            fetch(form.action, {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (payment === 'card') {
                                        saveBtn.innerHTML = `Оплатить заказ`;
                                    } else {
                                        saveBtn.innerHTML = `Оформить заказ`;
                                    }
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
                                        if (data.pay_url) {
                                            window.location.href = data.pay_url;
                                        } else {
                                            myModalSuccessOrder.open('#alertModal');
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Ошибка при отправке формы:', error);
                                });
                        }
                    });
                </script>
            </form>
        </div>
</section>
<script>

    class CheckoutCart {
        constructor(siteId, fUserId, userBonus) {
            this.siteId = siteId;
            this.fUserId = fUserId;
            this.userBonus = userBonus;

            this.items = this.initItems();

            // бонусы
            this.bonusApplied = false;
            this.bonusAmount = 0;

            // промокод
            this.promoApplied = false;
            this.promoCode = '';
            this.promoDiscount = 0;

            // суммы
            this.currentTotal = 0;
            this.deliveryCost = 0;
            this.maxBonus = 0;

            const totalEl = document.querySelector('.totalPrice strong');
            const totalElValue = document.querySelector('.totalPriceValue');
            if (totalEl) {
                this.currentTotal = parseFloat(totalEl.textContent.replace(/\D/g, '')) || 0;
            }

            this.updateMaxBonus();
            this.updateBonusPoints(this.currentTotal);

            this.initListeners();
            this.initBonusListeners();
            this.initPromoListeners();
        }

        initItems() {
            const items = {};
            document.querySelectorAll('.checkout__cart-item').forEach(item => {
                const id = item.id;
                const count = parseInt(item.querySelector('.countProduct').value);
                const priceEl = item.querySelector('.checkout__cart-price');
                items[id] = {id, count, priceEl};
            });
            return items;
        }

        initListeners() {
            document.querySelectorAll('.checkout__cart-quantity').forEach(wrapper => {
                wrapper.addEventListener('click', e => this.handleCountChange(e));
            });

            document.querySelectorAll('.checkout__cart-remove').forEach(btn => {
                btn.addEventListener('click', e => this.handleDelete(e));
            });
        }

        // ================= PROMO =================
        initPromoListeners() {
            const promoInput = document.querySelector('input[name="promo"]');
            if (!promoInput) return;

            const promoBtn = document.createElement('button');
            promoBtn.type = 'button';
            promoBtn.textContent = 'Применить';
            promoBtn.classList.add('border-btn');

            promoInput.after(promoBtn);

            promoBtn.addEventListener('click', async () => {
                const code = promoInput.value.trim();
                if (!code) return;

                if (!this.promoApplied) {
                    const result = await this.sendRequest('/ajax/applyPromo.php', {
                        promo: code,
                        total: this.currentTotal
                    });

                    if (result?.status === 'success') {
                        this.promoApplied = true;
                        this.promoCode = code;
                        this.promoDiscount = result.discount;

                        promoBtn.textContent = 'Отменить';
                        alert(result.message || 'Промокод активирован');

                    } else {
                        alert(result.message || 'Промокод недействителен');
                        return;
                    }
                } else {
                    this.promoApplied = false;
                    this.promoCode = '';
                    this.promoDiscount = 0;

                    promoBtn.textContent = 'Применить';
                    alert('Промокод отменён');

                }

                this.updateTotalWithAll();
            });
        }

        // ================= BONUS =================
        initBonusListeners() {
            const toggleBtn = document.querySelector('.promo__btn');
            const applyBtn = document.querySelector('#applyBonus');
            const bonusInput = document.querySelector('.promo__input');

            if (!toggleBtn || !applyBtn || !bonusInput) return;

            toggleBtn.addEventListener('click', () => {
                const block = document.querySelector('.promo__show');
                toggleBtn.classList.toggle('active');

                if (toggleBtn.classList.contains('active')) {
                    block.style.display = 'block';
                    bonusInput.max = this.maxBonus;
                    bonusInput.value = this.maxBonus;
                } else {
                    block.style.display = 'none';

                    if (this.bonusApplied) {
                        this.bonusApplied = false;
                        this.bonusAmount = 0;
                        applyBtn.textContent = 'Применить';
                        document.querySelector('#setBonus').value = 'N';
                        this.updateTotalWithAll();
                    }
                }
            });

            applyBtn.addEventListener('click', () => {
                if (!this.bonusApplied) {
                    let value = Math.min(bonusInput.value || 0, this.maxBonus);
                    this.bonusAmount = Math.max(value, 0);
                    this.bonusApplied = true;

                    applyBtn.textContent = 'Отменить';
                    document.querySelector('#setBonus').value = 'Y';
                } else {
                    this.bonusApplied = false;
                    this.bonusAmount = 0;

                    applyBtn.textContent = 'Применить';
                    document.querySelector('#setBonus').value = 'N';
                }

                this.updateTotalWithAll();
            });
        }

        updateMaxBonus() {
            let maxByTotal = Math.floor(this.currentTotal * 0.9);
            this.maxBonus = Math.min(maxByTotal, this.userBonus);

            const bonusInput = document.querySelector('.promo__input');
            if (bonusInput) bonusInput.max = this.maxBonus;

            if (this.bonusApplied && this.bonusAmount > this.maxBonus) {
                this.bonusAmount = this.maxBonus;
            }
        }

        updateBonusPoints(totalPrice) {
            const el = document.querySelector('.bonusPoints');
            const input = document.querySelector('.bonusPointsValue');
            const block = el?.closest('.checkout__param-item');

            if (!el || !input || !block) return;

            if (this.bonusApplied) {
                block.style.display = 'none';
                input.value = 0;
                return;
            }

            const percent = document.querySelector('#discountPercent').value || 0;
            const points = Math.floor(totalPrice * percent * 0.01);

            el.textContent = `+${points.toLocaleString('ru-RU')} баллов`;
            input.value = points;
            block.style.display = 'flex';
        }

        // ================= TOTAL =================
        updateTotalWithAll() {
            let total = this.currentTotal;

            total -= this.promoDiscount;
            total -= this.bonusAmount;
            total += this.deliveryCost;

            total = Math.max(total, 0);

            const totalEl = document.querySelector('.totalPrice strong');
            const totalElValue = document.querySelector('.totalPriceValue');
            if (totalEl) {
                totalEl.textContent = `${total.toLocaleString('ru-RU')} ₽`;
                totalElValue.value = `${total}`;
            }

            // промокод UI
            this.renderPromo();

            // бонусы UI
            this.renderBonus();

            this.updateBonusPoints(this.currentTotal);
        }

        renderPromo() {
            let block = document.querySelector('.promo-discount');

            if (this.promoDiscount > 0) {
                if (!block) {
                    block = document.createElement('div');
                    block.className = 'checkout__param-item promo-discount';
                    document.querySelector('.checkout__param').prepend(block);
                }
                block.innerHTML = `<p>Скидка по промокоду:</p><p>-${this.promoDiscount.toLocaleString('ru-RU')} ₽</p>`;
            } else {
                if (block) block.remove();
            }
        }

        renderBonus() {
            const block = document.querySelector('#applyBonusBlock');
            const text = document.querySelector('#applyBonusText');

            if (this.bonusAmount > 0) {
                block.style.display = 'flex';
                text.textContent = `-${this.bonusAmount.toLocaleString('ru-RU')} ₽`;
            } else {
                block.style.display = 'none';
            }
        }

        // ================= API =================
        async sendRequest(url, data) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data),
                });
                return await res.json();
            } catch (e) {
                console.error(e);
            }
        }

        async handleCountChange(e) {
            const item = e.target.closest('.checkout__cart-item');
            if (!item) return;

            const input = item.querySelector('.countProduct');
            const id = item.id;
            let value = parseInt(input.value);

            if (e.target.classList.contains('plus')) value++;
            else if (e.target.classList.contains('minus') && value > 1) value--;
            else return;

            input.value = value;
            this.items[id].count = value;

            const result = await this.sendRequest('/ajax/orderProduct.php', {
                id,
                count: value,
                siteId: this.siteId,
                fUserId: this.fUserId,
            });

            if (result?.totalPrice) {
                this.currentTotal = result.totalPrice;
                this.updateMaxBonus();
                this.updateTotalWithAll();
            }
        }

        async handleDelete(e) {
            const item = e.target.closest('.checkout__cart-item');
            if (!item) return;

            const id = item.id;
            item.remove();

            const result = await this.sendRequest('/ajax/orderProductDelete.php', {
                id,
                siteId: this.siteId,
                fUserId: this.fUserId,
            });

            if (result?.totalPrice) {
                this.currentTotal = result.totalPrice;
                this.updateMaxBonus();
                this.updateTotalWithAll();
            }
        }
    }

    // --- Инициализация ---
    //document.addEventListener('DOMContentLoaded', () => {
    //const cart = new CheckoutCart('<?=$siteId?>', '<?=$fUserId?>', <?=$userBonus?>);
    //});

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cart = new CheckoutCart('<?=$siteId?>', '<?=$fUserId?>', <?=$userBonus?>);
        cart.deliveryCost = 0; // добавляем поле для стоимости доставки

        const cityInput = document.querySelector('#city');
        const moskvaBlock = document.querySelector('#moskva');
        const otherCityInput = document.querySelector('#otherCity input');
        const deliveryInputs = document.querySelectorAll('input[name="delivery"]');
        const paymentInputs = document.querySelectorAll('input[name="payment"]');
        const addressBlock = document.querySelector('.address');
        const addressMore = document.querySelector('.address_more');
        const saveBtn = document.querySelector('#saveBtn');
        const cdekMap = document.querySelector('#cdek-map');

        // ====== Функция обновления состояния оплаты по городу ======
        function updateMoscowAvailability(value) {
            const isMoscow = value.trim().toLowerCase() === 'москва';
            const moskvaInput = moskvaBlock.querySelector('input');
            const checkmark = moskvaBlock.querySelector('.checkmark');

            moskvaBlock.style.color = isMoscow ? 'black' : 'grey';
            checkmark.style.border = `1px solid ${isMoscow ? 'black' : 'grey'}`;
            moskvaInput.disabled = !isMoscow;

            if (!isMoscow) otherCityInput.checked = true;
            //if (value.length > 3) loadRegion(value);
        }

        // ====== Функция управления блоками доставки ======
        function updateDeliveryBlocks() {
            const selected = document.querySelector('input[name="delivery"]:checked')?.value;
            //const isAddressHidden = ['137', '139'].includes(selected);
            const isAddressHidden = ['140'].includes(selected);
            const isMoscowDelivery = selected === '135';

            addressMore.style.display = isAddressHidden ? 'none' : 'flex';
            cdekMap.style.display = isAddressHidden ? 'flex' : 'none';
            cityInput.disabled = isMoscowDelivery;

            if (isMoscowDelivery) {

                cityInput.value = 'Москва';
                cart.deliveryCost = 0; // сохраняем в корзину
                cart.updateTotalWithAll();
                document.getElementById('cdek-price').innerHTML = `0 ₽`;
                document.getElementById('cdek').value = `N`;
            }
            if (isAddressHidden) {
                cityInput.value = '';
            }

            updateMoscowAvailability(cityInput.value);
        }

        // ====== Функция обновления кнопки оплаты ======
        function updatePaymentButton() {
            const selectedPayment = document.querySelector('input[name="payment"]:checked')?.value;
            saveBtn.textContent = selectedPayment === 'card'
                ? 'Оплатить заказ'
                : 'Оформить заказ';
        }

        // ====== Инициализация ======
        updateMoscowAvailability(cityInput.value);
        updateDeliveryBlocks();
        updatePaymentButton();

        // ====== Слушатели событий ======
        cityInput.addEventListener('input', e => updateMoscowAvailability(e.target.value));
        deliveryInputs.forEach(input => input.addEventListener('change', updateDeliveryBlocks));
        paymentInputs.forEach(input => input.addEventListener('change', updatePaymentButton));



        // === сдек ===

        let calculationTariff = {}; // сюда будем сохранять результат onCalculate
        let calculationAddress = {}; // сюда будем сохранять результат onCalculate

        new window.CDEKWidget({
            from: {
                country_code: 'RU',
                city: 'Москва',
                postal_code: 117105,
                code: 44,
                address: 'Варшавское шоссе, 26 с32',
            },
            root: 'cdek-map',
            apiKey: '0fd446ed-d771-44f9-a488-d51a25655491',
            servicePath: '<?=((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']?>/ajax/cdek/service.php',
            defaultLocation: 'Москва',
            lang: 'rus',
            currency: 'RUB',
            tariffs: { office: [136, 291], door: [137, 294] },
            // hideFilters: {
            //     is_dressing_room: true,
            // },
            goods: [{ width: 30, height: 20, length: 40, weight: 500 }],
            hideDeliveryOptions: { office: false, door: false },
            debug: false,
            // При расчете сохраняем цены
            onCalculate(tariffs, address) {
                //console.log('Расчет доставки:', result);
                calculationTariff = tariffs; // сохраняем данные
                calculationAddress = address; // сохраняем данные
            },

            // При выборе ПВЗ или тарифа выводим только выбранную цену
            onChoose(selected, tariff, address) {
                //console.log('Выбранная доставка:',calculationAddress);
                let cdek = document.getElementById('cdek');
                let city_cdek = document.getElementById('city_cdek');
                let city_code_cdek = document.getElementById('city_code_cdek');
                let tariff_cdek = document.getElementById('tariff_cdek');
                let address_cdek = document.getElementById('address_cdek');
                let pvz_code_cdek = document.getElementById('pvz_code_cdek');
                let postal_code = document.getElementById('postal_code_cdek');
                let formatted = document.getElementById('formatted_cdek');

                if (selected && calculationTariff[selected]) {
                    // console.log(address)
                    // console.log(tariff)

                    cdek.value = 'Y';

                    if(address.type === 'PVZ'){
                        pvz_code_cdek.value = address.code;

                        city_cdek.value = null;
                        city_code_cdek.value = null;
                        address_cdek.value = address.address;
                        postal_code.value = null;
                    } else {
                        pvz_code_cdek.value = null;

                        city_cdek.value = address.city;
                        address_cdek.value = address.formatted;
                        postal_code.value = address.postal_code;
                    }
                    tariff_cdek.value = tariff.tariff_code;

                    const cost = tariff.delivery_sum;
                    document.getElementById('cdek-price').innerHTML =
                        `${cost} ₽`;
                    document.getElementById('delivery-price').value = cost

                    cart.deliveryCost = cost; // сохраняем в корзину
                    cart.updateTotalWithAll();
                    if(address?.code === 44 || (typeof address !== 'undefined' && address?.name?.includes('Москва'))){
                        //updateMoscowAvailability('Москва')
                    } else {
                        updateMoscowAvailability('')
                    }
                } else {
                    pvz_code_cdek.value = null;
                    city_cdek.value = null;
                    city_code_cdek.value = null;
                    address_cdek.value = null;
                    postal_code.value = null;
                    tariff_cdek.value = null;

                    document.getElementById('cdek-price').innerHTML = `0 ₽`;
                    cart.deliveryCost = 0;
                    cart.updateTotalWithAll();
                }
            },
        });
    });
</script>
<div class="hystmodal" id="alertModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="  min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2"></p>
            </div>
        </div>
    </div>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
