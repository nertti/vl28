<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
?>
<?php
Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

$fUserId = Bitrix\Sale\Fuser::getId();
$siteId = Bitrix\Main\Context::getCurrent()->getSite();
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);


$order = Bitrix\Sale\Order::create($siteId, $fUserId);
$order->setPersonTypeId(1);
$order->setBasket($basket);
$order->setField('CURRENCY', Bitrix\Currency\CurrencyManager::getBaseCurrency());
$order->doFinalAction(true);

$totalQuantity = 0;
foreach ($basket as $item) {
    $totalQuantity += $item->getQuantity();
}
?>
<?php if (!empty($basket) !== 0): ?>
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
    <div class="cart-modal">
        <p class="cart-modal__count">
            Ваш выбор (<?= $totalQuantity ?>)
        </p>
        <div class="cart-modal__list">
            <?php foreach ($basket as $basketItem): ?>
                <?php
                $product = getProductInfo($basketItem->getField('PRODUCT_ID'));
                $product2 = getProductInfo($basketItem->getField('PRODUCT_XML_ID'));
                $propertySize = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'SIZE');
                $propertyColor = getElementProperties(2, $product2['ID'], 'COLOR');
                ?>
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
                <div class="cart-modal__item" id="<?= $basketItem->getField('ID') ?>">
                    <div class="cart-modal__left">
                        <img src="<?= CFile::getPath($product['PREVIEW_PICTURE']) ?>"
                             alt="<?= $product['NAME'] ?>">
                    </div>
                    <div class="cart-modal__right">
                        <p class="cart-modal__title"><?= $product['NAME'] ?></p>
                        <div class="cart-modal__param">
                            <p class="cart-modal__name">Цвет</p>
                            <p class="cart-modal__value">
                            <div class="cart__color">
                                <span style="background: #<?= $propertyColor['VALUE'] ?>;"></span>
                            </div>

                            </p>
                        </div>
                        <div class="cart-modal__param">
                            <p class="cart-modal__name">Размер</p>
                            <p class="cart-modal__value"><?= $propertySize['VALUE_ENUM'] ?></p>
                        </div>
                        <div class="cart-modal__param">
                            <p class="cart-modal__name">Количество:</p>
                            <p class="cart-modal__value"><?= $basketItem->getQuantity() ?></p>
                        </div>
                        <p class="cart-modal__price">
                            <?php if ($hasDiscount): ?>
                                <span style="text-decoration: line-through; color: #999; margin-right: 20px">
                                            <?= number_format($basePrice * $basketItem->getQuantity(), 0, '', ' ') ?> ₽
                                        </span>
                            <?php endif; ?>
                            <?= number_format($basketItem->getFinalPrice(), 0, '', ' ') ?>
                            ₽</p>
                        <a href="#"
                           class="cart-modal__remove"
                           data-product-id="<?= $basketItem->getProductId() ?>"
                           data-name="<?= htmlspecialcharsbx($product['NAME']) ?>"
                           data-image="<?= CFile::GetPath($product['PREVIEW_PICTURE']) ?>">
                            Убрать из корзины
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="cart-modal__footer">
            <div class="cart-modal__total">
                <p>Итого</p>
                <p><?= number_format($basket->getPrice(), 0, '', ' ') ?> ₽</p>
            </div>
            <a href="/basket/" class="border-btn">Перейти в корзину</a>
            <a href="/order/" class="black-btn">Оформить заказ</a>
        </div>
    </div>

<script>
    function updateHeaderBasketCountFly(count) {
        const cartLinks = document.querySelectorAll('.header__cart');
        if (!cartLinks.length) {
            return;
        }
        cartLinks.forEach(cartLink => {
            let counter = cartLink.querySelector('.header__cart-count');
            if (count <= 0) {
                if (counter) {
                    counter.remove();
                }
                return;
            }

            if (!counter) {
                counter = document.createElement('span');
                counter.className = 'header__cart-count';
                cartLink.appendChild(counter);
            }
            counter.textContent = count;
        });
    }

    document.addEventListener('click', async function (e) {

        const button = e.target.closest('.cart-modal__remove');

        if (!button) {
            return;
        }

        e.preventDefault();

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

            const item = button.closest('.cart-modal__item');

            // Обновляем счетчик товаров
            const countBlock = document.querySelector('.cart-modal__count');
            updateHeaderBasketCountFly(result.count);
            if (countBlock) {
                countBlock.textContent = `Ваш выбор (${result.count})`;
            }

            // Обновляем сумму
            const totalPrice = document.querySelector('.cart-modal__total p:last-child');
            if (totalPrice) {
                totalPrice.textContent =
                    Number(result.sum).toLocaleString('ru-RU') + ' ₽';
            }

            // Обновляем счетчик в шапке
            const headerCounter = document.querySelector('.header__cart-count');
            if (headerCounter) {
                headerCounter.textContent = result.count;
            }

            // Заполняем модалку
            const modalName = document.getElementById('removeBasketModalName');
            const modalImage = document.getElementById('removeBasketModalImage');

            if (modalName) {
                modalName.textContent = button.dataset.name;
            }

            if (modalImage) {
                modalImage.src = button.dataset.image;
                modalImage.alt = button.dataset.name;
            }

            // Удаляем элемент из списка
            item.remove();

            // Если корзина пустая
            if (result.count <= 0) {

                const cartModal = document.querySelector('.cart-modal');

                if (cartModal) {
                    cartModal.innerHTML = `
                    <div class="cart__inner">
                        <div class="cart__list">
                            <span class="bx-sbb-empty-cart-image"></span>
                            <span class="bx-sbb-empty-cart-text">Ваша корзина пуста</span>
                            <a href="/catalog/" class="bx-sbb-empty-cart-desc">В каталог</a>
                        </div>
                    </div>
                `;
                }
            }

            // Открываем HystModal
            if (window.hystModal) {
                hystModal.open('#removeBasketModal');
            }

        } catch (error) {
            console.error(error);
            alert('Ошибка соединения с сервером');
        }

    });
</script>

    <div class="hystmodal" id="removeBasketModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true">
                <button data-hystclose class="hystmodal__close"></button>

                <div class="thanks thanks-product">
                    <div class="thanks-product__image">
                        <img src="" alt="" id="removeBasketModalImage">
                    </div>

                    <p class="h2">Товар удалён из корзины!</p>

                    <p class="thanks-product__name" id="removeBasketModalName"></p>

                    <a href="/basket/">Перейти в корзину</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="cart-modal">
        <div class="cart__inner">
            <div class="cart__list">
                <span class="bx-sbb-empty-cart-image"></span>
                <span class="bx-sbb-empty-cart-text">Ваша корзина пуста</span>
                <a href="/catalog/" class="bx-sbb-empty-cart-desc">В каталог</a>
            </div>
        </div>
    </div>

<?php endif; ?>