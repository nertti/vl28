<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");

$fUserId = Bitrix\Sale\Fuser::getId();
$siteId = Bitrix\Main\Context::getCurrent()->getSite();
$basket = Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);
?>

<?php if (count($basket) !== 0): ?>
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
        //pr($arFavorites);
    } else {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arFavorites = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя

    }
    ?>
    <section class="cart first-section">
        <div class="container">
            <p class="h2">Корзина</p>
            <div class="cart__inner">
                <div class="cart__list">
                    <?php foreach ($basket as $basketItem): ?>
                        <div class="cart__item" id="<?= $basketItem->getField('ID') ?>">
                            <?php
                            $product = getProductInfo($basketItem->getField('PRODUCT_ID'));
                            $propertySize = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'SIZE');
                            $propertyColor = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'COLOR');
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
                                        <span style="background: <?= $propertyColor['VALUE_XML_ID'] ?>;"></span>
                                    </div>
                                </div>
                                <p class="cart__price"><?= number_format($basketItem->getFinalPrice(), 0, '', ' ') ?>
                                    ₽ </p>
                                <?php if (!$active): ?>
                                    <span class="cart__favorite favor"
                                          data-item="<?= explode('#', $basketItem->getField('PRODUCT_XML_ID'))[0] ?>">Добавить в избранное</span>
                                <?php else: ?>
                                    <span>Товар уже в избранном</span>
                                <?php endif; ?>
                            </div>
                            <a href="#" class="cart__remove pointer"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="/cart/order/" class="black-btn">Оформить заказ</a>
            </div>

        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteBtn = document.querySelectorAll('.cart__remove');
            deleteBtn.forEach(element => {
                element.addEventListener('click', handleDelete);
            });

            // Удаление товаров в корзине
            function handleDelete(event) {
                setTimeout(() => {
                    const wrapperProduct = event.target.closest('.cart__item');
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
                                wrapperProduct.style.display = 'none';
                                if(data.count === 0){
                                    location.reload();
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