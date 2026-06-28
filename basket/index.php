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

?>

<?php if (!empty($basket)): ?>
    <?php
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

    function getElementProperties($iblockId, $elementId, $code)
    {
        $db_props = CIBlockElement::GetProperty($iblockId, $elementId, "sort", "asc", array("CODE" => $code));
        if ($ar_props = $db_props->Fetch()) {
            return $ar_props;
        }
    }

    ?>
    <?php
    global $APPLICATION;
    global $USER;

    if (!$USER->IsAuthorized()) {
        $arFavorites = unserialize($APPLICATION->get_cookie("favorites"));
    } else {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arFavorites = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя

    }
        ?>
    <?php
        $offerIds = [];

        foreach ($basket as $basketItem) {
            $offerIds[] = (int)$basketItem->getProductId();
        }

        $offerIds = array_unique($offerIds);

        $offers = [];

        $res = ElementTable::getList([
                'filter' => [
                        '=IBLOCK_ID' => 5,
                        '@ID' => $offerIds
                ],
                'select' => [
                        'ID',
                        'IBLOCK_ID',
                        'NAME',
                        'IBLOCK_SECTION_ID'
                ]
        ]);

        while ($offer = $res->fetch()) {

            $offer['SIZE'] = getElementProperties(
                    5,
                    $offer['ID'],
                    'SIZE'
            );

            $offers[$offer['ID']] = $offer;
        }
        //pr($offers);
        ?>
    <section class="cart first-section">
        <div class="container">
            <p class="h2">Корзина</p>
            <div class="cart__inner">
                <div class="cart__list">
                    <?php foreach ($basket as $basketItem): ?>
                        <div class="cart__item" id="<?= $basketItem->getField('ID') ?>">
                            <?php
                            // Цена за единицу со скидкой (текущая)
                            $price = $basketItem->getPrice();

                            // Базовая цена за единицу (до скидки)
                            $basePrice = $basketItem->getBasePrice();

                            // Общая стоимость позиции (цена * количество)
                            $finalPrice = $basketItem->getFinalPrice();

                            // Проверяем, есть ли скидка
                            $hasDiscount = $basePrice > $price;
                            ?>
                            <?php
                            $product = getProductInfo($basketItem->getField('PRODUCT_ID'));
                            $product2 = getProductInfo($basketItem->getField('PRODUCT_XML_ID'));
                            $propertySize = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'SIZE');
                            $propertyColor = getElementProperties(2, $product2['ID'], 'COLOR');
//                            echo '<pre>';
//                            var_export($basketItem);
//                            echo '</pre>';

                            ?>
                            <?php
                            $active = false;
                            foreach ($arFavorites as $favorite) {
                                if ($favorite == explode('#', $basketItem->getField('PRODUCT_XML_ID'))[0]) {
                                    $active = true;
                                }
                            }
                            ?>
                            <div class="cart__left">
                                <img src="<?= CFile::getPath($product['PREVIEW_PICTURE']) ?>"
                                     alt="<?= $product['NAME'] ?>">
                            </div>
                            <div class="cart__right">
                                <p class="cart__title"><?= $product['NAME'] ?></p>
                                <div class="cart__param">
                                    <p class="cart__value"><?= $propertySize['VALUE_ENUM'] ?></p>
                                    <p class="cart__value"><?= $basketItem->getQuantity() ?> шт</p>
                                    <div class="cart__color">
                                        <span style="background: #<?= $propertyColor['VALUE'] ?>;"></span>
                                    </div>
                                </div>
                                <p class="cart__price">
                                    <?php if ($hasDiscount): ?>
                                        <span style="text-decoration: line-through; color: #999; margin-right: 20px">
                                            <?= number_format($basePrice * $basketItem->getQuantity(), 0, '', ' ') ?> ₽
                                        </span>
                                    <?php endif; ?>
                                    <?= number_format($finalPrice, 0, '', ' ') ?> ₽
                                </p>
                                <span
                                        class="cart__favorite favor <?= $active ? 'active' : '' ?>"
                                        data-item="<?= explode('#', $basketItem->getField('PRODUCT_XML_ID'))[0] ?>"
                                        data-name="<?= htmlspecialcharsbx($product['NAME']) ?>"
                                        data-image="<?= CFile::GetPath($product['PREVIEW_PICTURE']) ?>"
                                >
                                    <?= $active ? 'Товар уже в избранном' : 'Добавить в избранное' ?>
                                </span>
                            </div>
                            <a href="#"
                               class="cart__remove pointer"
                               data-product-id="<?= $basketItem->getProductId() ?>"
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
<?php
    ?>
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