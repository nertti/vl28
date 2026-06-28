<?php
/** @var \CMain $APPLICATION */
/** @var bool $isCartPage */
/** @var bool $isOrderPage */
$isCartPage = $APPLICATION->GetCurPage(false) === '/basket/';
$isOrderPage = $APPLICATION->GetCurPage(false) === '/order/';
?>
<?php if (!$isCartPage && !$isOrderPage): ?>
    <div class="hystmodal hystmodal_header" id="cartModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window" role="dialog" aria-modal="true">
                <button data-hystclose class="hystmodal__close"></button>
                <div id="cartModalContent">
                    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ajax/basket/updateFlyBasket.php'; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
