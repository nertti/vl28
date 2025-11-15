<?php

/** @var \CMain $APPLICATION */
/** @var \CMain $USER */
/** @global  $userBonus */
/** @global  $discountCard */
/** @global  $discountPercent */


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();

Bitrix\Main\Loader::includeModule("Sale");
Bitrix\Main\Loader::includeModule("Catalog");

use Bitrix\Sale\Delivery\Services\Manager;

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

if (empty($basket->getQuantityList())) {
    header('Location: /catalog/');
}
$fullPrice = $basket->getBasePrice();
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
                <input id="discountCard" type="hidden" name="discountCard" value="<?= $discountCard ?>">
                <input id="discountPercent" type="hidden" name="discountPercent" value="<?= $discountPercent ?>">
                <div class="checkout__form-left">
                    <div class="checkout__label">
                        <p class="checkout__name">E-mail</p>
                        <div class="checkout__inputs">
                            <p class="error-text email" style="display: none;">Пожалуйста, введите свой email.</p>
                            <input type="text" class="form-input checkout__input email" placeholder="E-mail"
                                   name="email" value="<?=$arUser['EMAIL']?>">
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
                            <input type="text" name="name" class="form-input checkout__input name" placeholder="Имя" value="<?=$arUser['NAME']?>">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Фамилия</p>
                        <div class="checkout__inputs">
                            <p class="error-text surname" style="display: none;">Пожалуйста, введите свою фамилию.</p>
                            <input type="text" name="surname" class="form-input checkout__input surname"
                                   placeholder="Фамилия" value="<?=$arUser['LAST_NAME']?>">
                        </div>
                    </div>
                    <div class="checkout__label">
                        <p class="checkout__name">Телефон</p>
                        <div class="checkout__inputs">
                            <p class="error-text phone" style="display: none;">Пожалуйста, введите свой телефон.</p>
                            <input type="text" name="phone" class="form-input phone-input checkout__input phone"
                                   placeholder="+7 (___) ___-__-__" value="<?=$arUser['PERSONAL_PHONE']?>">
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
<!--                        <div class="checkout__param-item">-->
<!--                            <p>Доставка:</p>-->
<!--                            <p>0 ₽</p>-->
<!--                        </div>-->
                        <div class="checkout__param-item">
                            <p>Скидка по промокоду:</p>
                            <p>0 ₽</p>
                        </div>
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
                            <strong><?= $fullPrice; ?> ₽</strong>
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
            this.userBonus = userBonus; // Количество бонусов у пользователя
            this.items = this.initItems();
            this.bonusApplied = false;
            this.bonusAmount = 0;
            this.currentTotal = 0;
            this.maxBonus = 0;

            const totalEl = document.querySelector('.totalPrice strong');
            if (totalEl) {
                this.currentTotal = parseFloat(totalEl.textContent.replace(/\D/g, '')) || 0;
                this.updateMaxBonus();
                this.updateBonusPoints(this.currentTotal);
            }

            this.initListeners();
            this.initBonusListeners();
        }

        initItems() {
            const items = {};
            document.querySelectorAll('.checkout__cart-item').forEach(item => {
                const id = item.id;
                const count = parseInt(item.querySelector('.countProduct').value);
                const priceEl = item.querySelector('.checkout__cart-price');
                items[id] = { id, count, priceEl };
            });
            //console.log('Корзина инициализирована:', items);
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

        initBonusListeners() {
            const toggleBtn = document.querySelector('.promo__btn');
            const applyBtn = document.querySelector('#applyBonus');
            const bonusInput = document.querySelector('.promo__input');

            if (!toggleBtn || !applyBtn || !bonusInput) return;

            // Тогл блока бонусов
            toggleBtn.addEventListener('click', () => {
                const block = document.querySelector('.promo__show');
                block.style.display = 'none';
                toggleBtn.classList.toggle('active');

                if (toggleBtn.classList.contains('active')) {
                    if (block) block.style.display = 'block';
                    // Подставляем максимально возможное списание
                    let value = this.bonusApplied ? this.bonusAmount : this.maxBonus;
                    bonusInput.max = this.maxBonus;
                    bonusInput.value = this.maxBonus;

                    //console.log(`Бонусы для списания подставлены: ${bonusInput.value} ₽ (макс: ${this.maxBonus})`);
                } else {
                    if (block) block.style.display = 'none';
                    if (this.bonusApplied) {
                        this.bonusAmount = 0;
                        this.bonusApplied = false;
                        if (applyBtn) applyBtn.textContent = 'Применить';
                        document.querySelector('#setBonus').value = 'N';
                        this.updateBonusPoints(this.currentTotal);
                        this.updateTotalWithBonus();
                        //console.log('Списание бонусов отменено через выключение тогл, сумма пересчитана');
                    }
                }
            });

            // Кнопка "Применить/Отменить"
            applyBtn.addEventListener('click', () => {
                if (!this.bonusApplied) {
                    let applyValue = Math.min(bonusInput.value || 0, this.maxBonus);
                    applyValue = Math.max(applyValue, 0);

                    this.bonusAmount = applyValue;
                    this.bonusApplied = true;

                    applyBtn.textContent = 'Отменить';
                    document.querySelector('#setBonus').value = 'Y';

                    //console.log(`Применены бонусы: ${this.bonusAmount} ₽`);
                } else {
                    this.bonusAmount = 0;
                    this.bonusApplied = false;

                    applyBtn.textContent = 'Применить';
                    document.querySelector('#setBonus').value = 'N';

                    //console.log(`Списание бонусов отменено`);
                }

                this.updateBonusPoints(this.currentTotal);
                this.updateTotalWithBonus();
            });
        }

        getTotalPriceWithoutBonus() {
            return this.currentTotal;
        }

        updateTotalWithBonus() {
            const totalEl = document.querySelector('.totalPrice strong');
            const applyText = document.querySelector('#applyBonusText');
            const totalAfter = Math.max(this.currentTotal - this.bonusAmount, 0);

            if (totalEl) totalEl.textContent = `${totalAfter} ₽`;
            if (applyText) applyText.textContent = `-${this.bonusAmount} ₽`;
            if (this.bonusAmount > 0) {
                document.querySelector('#applyBonusBlock').style.display = 'flex';
            } else {
                document.querySelector('#applyBonusBlock').style.display = 'none';
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

            //console.log(`Изменено количество товара ${id}: ${value}`);

            const result = await this.sendRequest('/ajax/orderProduct.php', {
                id,
                count: value,
                siteId: this.siteId,
                fUserId: this.fUserId,
            });

            if (result?.status !== 'error') {
                if (result.price) this.updateItemPrice(id, result.price);
                if (result.totalPrice) {
                    this.currentTotal = result.totalPrice;
                    this.updateTotalPrice(this.currentTotal);
                    this.updateMaxBonus();
                    this.updateBonusPoints(this.currentTotal);
                    if (this.bonusApplied) this.updateTotalWithBonus();
                }
            }
        }

        async handleDelete(e) {
            const item = e.target.closest('.checkout__cart-item');
            if (!item) return;

            const id = item.id;
            delete this.items[id];
            item.remove();

            //console.log(`Товар ${id} удалён из корзины`);

            const result = await this.sendRequest('/ajax/orderProductDelete.php', {
                id,
                siteId: this.siteId,
                fUserId: this.fUserId,
            });

            if (result?.status !== 'error' && result.totalPrice) {
                this.currentTotal = result.totalPrice;
                this.updateTotalPrice(this.currentTotal);
                this.updateMaxBonus();
                this.updateBonusPoints(this.currentTotal);
                if (this.bonusApplied) this.updateTotalWithBonus();
            }
        }

        updateMaxBonus() {
            let maxByTotal = Math.floor(this.currentTotal * 0.9);
            this.maxBonus = Math.min(maxByTotal, this.userBonus);

            const bonusInput = document.querySelector('.promo__input');
            if (bonusInput) bonusInput.max = this.maxBonus;

            if (this.bonusApplied && this.bonusAmount > this.maxBonus) {
                this.bonusAmount = this.maxBonus;
                this.updateTotalWithBonus();
                const applyBtn = document.querySelector('#applyBonus');
                if (applyBtn) applyBtn.textContent = 'Отменить';
                //console.log(`Списанные бонусы уменьшены до нового максимума: ${this.bonusAmount} ₽`);
            }
        }

        async sendRequest(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                //console.log('Ответ сервера:', result);
                return result;
            } catch (error) {
                console.error('Ошибка запроса:', error);
            }
        }

        updateItemPrice(id, newPrice) {
            const item = this.items[id];
            if (!item) return;
            item.priceEl.textContent = `${newPrice} ₽`;
            //console.log(`Цена товара ${id} обновлена: ${newPrice} ₽`);
        }

        updateTotalPrice(total) {
            const totalEl = document.querySelector('.totalPrice strong');
            if (totalEl) totalEl.textContent = `${total} ₽`;
            //console.log(`Общая сумма корзины: ${total} ₽`);
        }

        updateBonusPoints(totalPrice) {
            const bonusTextEl = document.querySelector('.bonusPoints');
            const bonusInputEl = document.querySelector('.bonusPointsValue');
            const bonusBlockEl = bonusTextEl?.closest('.checkout__param-item');

            if (!bonusTextEl || !bonusInputEl || !bonusBlockEl) return;

            if (this.bonusApplied) {
                bonusTextEl.textContent = '+0 баллов';
                bonusInputEl.value = 0;
                bonusBlockEl.style.display = 'none';
                //console.log('Бонусы списаны, начисление скрыто');
                return;
            }

            const discountPercent = document.querySelector('#discountPercent').value || '';
            let points = 0;

            if(discountPercent){
                points = Math.floor(totalPrice * discountPercent * 0.01);
            }

            bonusTextEl.textContent = `+${points} баллов`;
            bonusInputEl.value = points;
            bonusBlockEl.style.display = 'flex';

            //console.log(`Начисляемые бонусы: ${points} баллов`);
        }
    }

    // --- Инициализация ---
    document.addEventListener('DOMContentLoaded', () => {
        const cart = new CheckoutCart('<?=$siteId?>', '<?=$fUserId?>', <?=$userBonus?>);
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cityInput = document.querySelector('#city');
        const moskvaBlock = document.querySelector('#moskva');
        const otherCityInput = document.querySelector('#otherCity input');
        const deliveryInputs = document.querySelectorAll('input[name="delivery"]');
        const paymentInputs = document.querySelectorAll('input[name="payment"]');
        const addressBlock = document.querySelector('.address');
        const addressMore = document.querySelector('.address_more');
        const saveBtn = document.querySelector('#saveBtn');

        // ====== Функция обновления состояния оплаты по городу ======
        function updateMoscowAvailability(value) {
            const isMoscow = value.trim().toLowerCase() === 'москва';
            const moskvaInput = moskvaBlock.querySelector('input');
            const checkmark = moskvaBlock.querySelector('.checkmark');

            moskvaBlock.style.color = isMoscow ? 'black' : 'grey';
            checkmark.style.border = `1px solid ${isMoscow ? 'black' : 'grey'}`;
            moskvaInput.disabled = !isMoscow;

            if (!isMoscow) otherCityInput.checked = true;
        }

        // ====== Функция управления блоками доставки ======
        function updateDeliveryBlocks() {
            const selected = document.querySelector('input[name="delivery"]:checked')?.value;
            const isAddressHidden = ['137', '139'].includes(selected);
            const isMoscowDelivery = selected === '135';

            addressMore.style.display = isAddressHidden ? 'none' : 'flex';
            cityInput.disabled = isMoscowDelivery;

            if (isMoscowDelivery) {
                cityInput.value = 'Москва';
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
    });
</script>

<!--
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cityInput = document.querySelector('#city');
        const moskvaBlock = document.querySelector('#moskva');
        const moskvaInput = moskvaBlock.querySelector('input');
        const moskvaCheckmark = moskvaBlock.querySelector('.checkmark');
        const otherCityInput = document.querySelector('#otherCity input');
        const deliveryInputs = document.querySelectorAll('input[name="delivery"]');
        const paymentInputs = document.querySelectorAll('input[name="payment"]');
        const addressMore = document.querySelector('.address_more');
        const saveBtn = document.querySelector('#saveBtn');

        // ====== Функция обновления состояния города ======
        function updateMoscowAvailability(city) {
            const isMoscow = city.trim().toLowerCase() === 'москва';
            moskvaBlock.classList.toggle('disabled', !isMoscow);
            moskvaInput.disabled = !isMoscow;
            if (!isMoscow) otherCityInput.checked = true;
        }

        // ====== Функция управления блоками доставки ======
        function updateDeliveryBlocks() {
            const selected = document.querySelector('input[name="delivery"]:checked')?.value;
            const isAddressHidden = ['137', '139'].includes(selected);
            const isMoscowDelivery = selected === '135';

            addressMore.style.display = isAddressHidden ? 'none' : 'flex';
            cityInput.disabled = isMoscowDelivery;
            if (isMoscowDelivery) cityInput.value = 'Москва';

            updateMoscowAvailability(cityInput.value);
        }

        // ====== Функция обновления кнопки оплаты ======
        function updatePaymentButton() {
            const selectedPayment = document.querySelector('input[name="payment"]:checked')?.value;
            saveBtn.textContent = selectedPayment === 'card' ? 'Оплатить заказ' : 'Оформить заказ';
        }

        // ====== Инициализация ======
        updateDeliveryBlocks();
        updatePaymentButton();

        // ====== Слушатели событий ======
        cityInput.addEventListener('input', e => updateMoscowAvailability(e.target.value));
        deliveryInputs.forEach(input => input.addEventListener('change', updateDeliveryBlocks));
        paymentInputs.forEach(input => input.addEventListener('change', updatePaymentButton));
    });

</script>
-->
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
