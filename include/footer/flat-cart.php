<?php
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
<div class="hystmodal hystmodal_header" id="cartModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="cart-modal">
                <p class="cart-modal__count">Ваш выбор (<?=count($basket)?>)</p>
                <div class="cart-modal__list">
                    <?php foreach ($basket as $basketItem): ?>
                        <?php
                        $product = getProductInfo($basketItem->getField('PRODUCT_ID'));
                        $propertySize = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'SIZE');
                        $propertyColor = getElementProperties($product['IBLOCK_ID'], $product['ID'], 'COLOR');
                        ?>
                    <div class="cart-modal__item" id="<?= $basketItem->getField('ID') ?>">
                        <div class="cart-modal__left">
                            <img src="<?= CFile::getPath($product['PREVIEW_PICTURE']) ?>" alt="<?= $product['NAME'] ?>">
                        </div>
                        <div class="cart-modal__right">
                            <p class="cart-modal__title"><?= $product['NAME'] ?></p>
                            <div class="cart-modal__param">
                                <p class="cart-modal__name">Цвет</p>
                                <p class="cart-modal__value"><?= $propertyColor['VALUE_ENUM'] ?></p>
                            </div>
                            <div class="cart-modal__param">
                                <p class="cart-modal__name">Размер</p>
                                <p class="cart-modal__value"><?= $propertySize['VALUE_ENUM'] ?></p>
                            </div>
                            <div class="cart-modal__param">
                                <p class="cart-modal__name">Количество:</p>
                                <p class="cart-modal__value"><?= $basketItem->getQuantity() ?></p>
                            </div>
                            <p class="cart-modal__price"><?= number_format($basketItem->getFinalPrice(), 0, '', ' ') ?> ₽</p>
                            <a href="#" class="cart-modal__remove">Убрать из корзины</a>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
                <div class="cart-modal__footer">
                    <div class="cart-modal__total">
                        <p>Итого</p>
                        <p><?=$basket->getPrice()?> ₽</p>
                    </div>
                    <a href="/cart/" class="border-btn">Перейти в корзину</a>
                    <a href="/cart/order/" class="black-btn">Оформить заказ</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteBtn = document.querySelectorAll('.cart-modal__remove');
            deleteBtn.forEach(element => {
                element.addEventListener('click', handleDelete);
            });

            // Удаление товаров в корзине
            function handleDelete(event) {
                setTimeout(() => {
                    const wrapperProduct = event.target.closest('.cart-modal__item');
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

<?php endif;?>