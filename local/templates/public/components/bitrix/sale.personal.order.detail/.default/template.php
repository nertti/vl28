<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @global CMain $APPLICATION */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateFolder */

?>

<?php
//pr($arResult);
?>
<div class="account__default account__default_borderless">
    <div class="account__orders">
        <div class="account__list">
            <div class="account__order account__order_big">
                <p class="account__order-title">Заказ #<?= $arResult['ID'] ?></p>
                <div class="account__order-wrap">
                    <?php if ($arResult['STATUS']['ID'] == 'N'): ?>
                        <?php foreach ($arResult['ORDER_PROPS'] as $prop): ?>
                            <?php if ($prop['ID'] == 11): ?>
                                <a href="<?= $prop['VALUE'] ?>" class="link">Ссылка на оплату</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php elseif($arResult['STATUS']['ID'] == 'P' || $arResult['STATUS']['ID'] == 'N'):?>
                        <form action="/ajax/cancelPay.php" id="form">
                            <?php foreach ($arResult['ORDER_PROPS'] as $prop): ?>
                                <?php if ($prop['ID'] == 26 || $prop['ID'] == 25 || $prop['ID'] == 24): ?>
                                    <input type="hidden" name="<?=$prop['CODE']?>" value="<?=$prop['VALUE']?>">
                                <?php endif; ?>
                                <input type="hidden" name="ID" value="<?=$arResult['ID']?>">
                            <?php endforeach; ?>
                            <span class="link cancelOrder pointer">Отменить заказ</span>
                        </form>
                    <?php endif; ?>
                    <div class="account__product">

                        <?php foreach ($arResult['BASKET'] as $item): ?>
                            <?php
                            $productId = $item['PRODUCT_ID'];
                            $element = CIBlockElement::GetByID($productId)->GetNext();
                            $previewImage = CFile::GetFileArray($element['PREVIEW_PICTURE']);
                            ?>
                            <div class="product">
                                <img src="<?= $previewImage['SRC'] ?>" alt="T-SHIRT DENSE BLACK 100"
                                     class="product__img">
                                <div class="product__inner">
                                    <p class="product__title"><?= $item['NAME'] ?></p>
                                    <!--                                    <p class="product__price">-->
                                    <?php //= $item['BASE_PRICE'] ?><!-- ₽</p>-->
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="account__product-params">
                            <div class="account__product-param">
                                <strong>Дата</strong>
                                <p><?= $arResult['DATE_INSERT'] ?></p>
                            </div>
                            <?php if ($arResult['STATUS']['ID'] == 'N'): ?>
                                <div class="account__product-param">
                                    <strong>Сумма заказа (с учётом скидок)</strong>
                                    <p><?= $arResult['PRICE'] - $arResult['SUM_PAID'] ?> ₽</p>
                                </div>
                            <?php endif; ?>
                            <div class="account__product-param">
                                <strong>Товаров</strong>
                                <p><?= count($arResult['BASKET']) ?> шт.</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Статус</strong>
                                <p><?= $arResult['STATUS']['NAME'] ?></p>
                            </div>
                            <!--
                            <div class="account__product-param">
                                <strong>Размер</strong>
                                <p>S</p>
                            </div>
                            <div class="account__product-param">
                                <strong>Цвет</strong>
                                <div class="account__product-param_color">
                                    <span style="background: #000;"></span>
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cancelOrder = new HystModal({
            linkAttributeName: 'data-hystmodal',
            afterClose: function (modal) {
                window.location = '/profile/order-list/'
            },
        });

        const form = document.querySelector('#form');
        const btn = document.querySelector('.cancelOrder');
        // Если форма найдена, добавляем слушатель события submit
        if (form) {
            btn.addEventListener('click', handleFormSubmit);
        } else {
            console.warn('Форма не найдена на странице');
        }

        function handleFormSubmit(event) {
            event.preventDefault();
            btn.innerHTML = `
                  <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                  <span role='status'>Отменяем...</span>
                `;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    btn.innerHTML = `Отменить заказ`;
                    if (data.status === 'error') {
                        console.log('Ошибка отмены оплаты');
                    } else {
                        if(data.data.Success === '1'){
                            cancelOrder.open('#alertModal');
                        } else {
                            cancelOrder.open('#alertModal2');
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка при отправке формы:', error);
                });
        }
    })
    ;
</script>
<div class="hystmodal" id="alertModal2" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="  min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2">Заказ успешно отменён!</p>
                <a href="/catalog/" class="text link">В каталог</a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="alertModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="  min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2">Заказ уже отменён!</p>
                <a href="/catalog/" class="text link">В каталог</a>
            </div>
        </div>
    </div>
</div>