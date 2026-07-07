<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");

use Bitrix\Sale;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Iblock\ElementTable;


Loader::includeModule('sale');
Loader::includeModule('catalog');

$fUserId = Bitrix\Sale\Fuser::getId();
$siteId = Bitrix\Main\Context::getCurrent()->getSite();
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);

$order = Order::create($siteId, $fUserId);
$order->setPersonTypeId(1);
$order->setBasket($basket);
$order->setField('CURRENCY', CurrencyManager::getBaseCurrency());
$order->doFinalAction(true);
// Чтобы высчитать скидку
?>

<?php if ($basket->count()): ?>
    <?php
    global $APPLICATION;
    global $USER;

    $arFavorites = [];

    if (!$USER->IsAuthorized()) {
        $cookie = $APPLICATION->get_cookie("favorites");
        if ($cookie) {
            $arFavorites = unserialize($cookie);
        }
    } else {
        $arFavorites = CUser::GetByID($USER->GetID())
                ->Fetch()['UF_FAVORITES'] ?? [];
    }
    ?>
    <?php
    $basketData = new BasketData($basket);
    $basketData->setFavorites($arFavorites);
    $items = $basketData->getItems();
    ?>
    <section class="cart first-section">
        <div class="container">
            <p class="h2">Корзина</p>
            <div class="cart__inner">
                <div class="cart__list">
                    <?php foreach ($items as $item): ?>
                        <?php
                        $basketItem = $item['BASKET'];
                        $product = $item['PRODUCT'];
                        $offer = $item['OFFER'];
                        $propertySize = $item['SIZE'];
                        $propertyColor = $item['COLOR'];

                        $price = $item['PRICE'];
                        $basePrice = $item['BASE_PRICE'];
                        $finalPrice = $item['FINAL_PRICE'];

                        $hasDiscount = $item['HAS_DISCOUNT'];

                        $quantity = $item['QUANTITY'];
                        ?>
                        <div class="cart__item" id="<?= $product['ID'] ?>">
                            <div class="cart__left">
                                <img src="<?= CFile::getPath($offer['PREVIEW_PICTURE']) ?>"
                                     alt="<?= $product['NAME'] ?>">
                            </div>
                            <div class="cart__middle">
                                <p class="cart__title"><?= $product['NAME'] ?></p>
                                <div class="cart__param">
                                    <p class="cart__value"><?= $propertySize ?></p>
                                    <div class="cart__color">
                                        <span style="background: #<?= $propertyColor ?>;"></span>
                                    </div>
                                </div>
                                <p class="cart__price">
                                    <?php if ($item['HAS_DISCOUNT']): ?>
                                        <span class="cart__price-old">
                                            <?= number_format($item['SUM_BASE_PRICE'], 0, '', ' ') ?> ₽
                                        </span>
                                    <?php endif; ?>

                                    <span class="cart__price-current">
                                        <?= number_format($item['SUM_PRICE'], 0, '', ' ') ?> ₽
                                    </span>
                                </p>
                                <span
                                        class="cart__favorite favor <?= $item['IS_FAVORITE'] ? 'active' : '' ?>"
                                        data-item="<?= $offer['ID'] ?>"
                                        data-name="<?= htmlspecialcharsbx($product['NAME']) ?>"
                                        data-image="<?= CFile::GetPath($offer['PREVIEW_PICTURE']) ?>"
                                >
                                    <?= $item['IS_FAVORITE'] ? 'Товар уже в избранном' : 'Добавить в избранное' ?>
                                </span>
                            </div>
                            <div class="cart__right">
                                <div class="checkout__cart-quantity" data-basket-id="<?= $basketItem->getId() ?>">
                                    <div class="minus"></div>
                                    <input type="number" min="1" max="30" class="checkout__cart-input countProduct"
                                           value="<?= $quantity ?>">
                                    <div class="plus"></div>
                                </div>
                            </div>
                            <a href="#"
                               class="cart__remove pointer"
                               data-product-id="<?= $offer['ID'] ?>"
                               data-name="<?= htmlspecialcharsbx($product['NAME']) ?>"
                               data-image="<?= CFile::GetPath($product['PREVIEW_PICTURE']) ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="/order/" class="black-btn">Оформить заказ</a>
            </div>

        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', async function (e) {
                const button = e.target.closest('.plus, .minus');
                if (!button) {
                    return;
                }
                const wrapper = button.closest('.checkout__cart-quantity');
                const input = wrapper.querySelector('.countProduct');
                let quantity = parseInt(input.value);
                if (button.classList.contains('plus')) {
                    quantity++;
                }
                if (button.classList.contains('minus')) {
                    quantity--;
                    if(quantity < 1){
                        quantity = 1;
                    }
                }
                input.value = quantity;
                await updateQuantity(
                    wrapper.dataset.basketId,
                    quantity,
                    wrapper
                );
            });
        });
        async function updateQuantity(
            basketId,
            quantity,
            wrapper
        ){
            try {
                const response = await fetch(
                    '/ajax/basket/updateQuantity.php',
                    {
                        method:'POST',
                        headers:{
                            'Content-Type':
                                'application/x-www-form-urlencoded'
                        },
                        body:new URLSearchParams({
                            BASKET_ID:basketId,
                            QUANTITY:quantity
                        })
                    }
                );
                const result = await response.json();
                if(!result.success){
                    return;
                }
                // цена товара
                const cartItem =
                    wrapper.closest('.cart__item');
                const currentPrice =
                    cartItem.querySelector('.cart__price-current');
                if(currentPrice){
                    currentPrice.textContent =
                        numberFormat(result.itemPrice) + ' ₽';

                }
                const oldPrice =
                    cartItem.querySelector('.cart__price-old');
                if(oldPrice){
                    if(result.hasDiscount){
                        oldPrice.style.display = 'inline';
                        oldPrice.textContent =
                            numberFormat(result.itemBasePrice) + ' ₽';
                    } else {
                        oldPrice.style.display = 'none';
                    }
                }
                // общая сумма корзины
                const total =
                    document.querySelector('.cart-total');
                if(total){
                    total.textContent =
                        numberFormat(result.totalPrice) + ' ₽';
                }
            }
            catch(e){
                console.error(e);
            }
        }
        function numberFormat(number){

            return new Intl.NumberFormat('ru-RU')
                .format(number);

        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('click', async function (e) {

                const button = e.target.closest('.cart__remove');

                if (!button) {
                    return;
                }

                e.preventDefault();

                const productItem = button.closest('.cart__item');

                try {

                    const response = await fetch('/ajax/basket/delFromBasket.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: new URLSearchParams({
                            PRODUCT_ID: button.dataset.productId
                        })
                    });

                    const result = await response.json();

                    if (result.status !== 'success') {
                        alert('Ошибка удаления товара');
                        return;
                    }

                    // Удаляем карточку
                    productItem.remove();

                    // Показываем модалку удаления
                    const modalName = document.getElementById('removeBasketModalName');
                    const modalImage = document.getElementById('removeBasketModalImage');

                    if (modalName) {
                        modalName.textContent = button.dataset.name;
                    }

                    if (modalImage) {
                        modalImage.src = button.dataset.image;
                        modalImage.alt = button.dataset.name;
                    }

                    if (window.hystModal) {
                        hystModal.open('#removeBasketModal');
                    }

                    // Если корзина пустая
                    if (result.count <= 0) {

                        const cartList = document.querySelector('.cart__list');

                        cartList.innerHTML = `
                <span class="bx-sbb-empty-cart-image"></span>
                <span class="bx-sbb-empty-cart-text">Ваша корзина пуста</span>
                <a href="/catalog/" class="bx-sbb-empty-cart-desc">В каталог</a>
            `;
                        const orderButton = document.querySelector('.black-btn');

                        if (orderButton) {
                            orderButton.remove();
                        }
                    }

                    // Обновляем счетчик в шапке
                    const headerCounter = document.querySelector('.header__cart-count');

                    if (headerCounter) {
                        headerCounter.textContent = result.count;
                    }

                    // Обновляем плавающую корзину если она открыта
                    const modalCounter = document.querySelector('.cart-modal__count');

                    if (modalCounter) {
                        modalCounter.textContent = `Ваш выбор (${result.count})`;
                    }

                } catch (error) {
                    console.error(error);
                    alert('Ошибка соединения с сервером');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const myModal = new HystModal({
                linkAttributeName: 'data-hystmodal',
                beforeOpen() {

                    const scrollbarWidth =
                        window.innerWidth -
                        document.documentElement.clientWidth;

                    document.body.style.paddingRight =
                        scrollbarWidth + 'px';
                },

                afterClose() {

                    document.body.style.paddingRight = '';
                }
            });
            document.addEventListener('click', async function (e) {
                const favoriteBtn = e.target.closest('.cart__favorite.favor');

                if (!favoriteBtn) {
                    return;
                }

                e.preventDefault();

                const productId = favoriteBtn.dataset.item;
                const productName = favoriteBtn.dataset.name;
                const productImage = favoriteBtn.dataset.image;

                try {

                    const response = await fetch('/ajax/catalog.element/favorite.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + encodeURIComponent(productId)
                    });

                    const result = await response.json();

                    if (!result.success) {
                        return;
                    }

                    if (result.action === 'add') {

                        favoriteBtn.classList.add('active');
                        favoriteBtn.textContent = 'Товар уже в избранном';

                        document.getElementById('favoriteImage').src = productImage;
                        document.getElementById('favoriteImage').alt = productName;
                        document.getElementById('favoriteName').textContent = productName;
                        myModal.open('#favoriteModal');
                    }

                    if (result.action === 'delete') {

                        favoriteBtn.classList.remove('active');
                        favoriteBtn.textContent = 'Добавить в избранное';

                        document.getElementById('delFavoriteImage').src = productImage;
                        document.getElementById('delFavoriteImage').alt = productName;
                        document.getElementById('delFavoriteName').textContent = productName;

                        myModal.open('#delFavoriteModal');
                    }

                    const favoriteCounter = document.querySelector('.favorite-count');

                    if (favoriteCounter) {
                        favoriteCounter.textContent = result.count;
                    }

                } catch (error) {
                    console.error(error);
                }

            });
        });
    </script>

    <div class="hystmodal" id="favoriteModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe">
                <button data-hystclose class="hystmodal__close"></button>

                <div class="thanks thanks-product vertical-modal">
                    <div class="thanks-product__image">
                        <img src="" alt="" id="favoriteImage">
                    </div>

                    <p class="h2">Товар добавлен в избранное!</p>

                    <p id="favoriteName"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="delFavoriteModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
                <button data-hystclose class="hystmodal__close"></button>

                <div class="thanks thanks-product vertical-modal">
                    <div class="thanks-product__image">
                        <img src="" alt="" id="delFavoriteImage">
                    </div>

                    <p class="h2">Товар удалён из избранного!</p>

                    <p id="delFavoriteName"></p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <section class="cart first-section">
        <div class="container">
            <p class="h2">Корзина</p>
            <div class="cart__inner">
                <div class="cart__list">
                    <span class="bx-sbb-empty-cart-image"></span>
                    <span class="bx-sbb-empty-cart-text">Ваша корзина пуста</span>
                    <a href="/catalog/" class="bx-sbb-empty-cart-desc">В каталог</a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>