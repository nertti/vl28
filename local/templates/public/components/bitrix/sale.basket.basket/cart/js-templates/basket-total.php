<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
    <div class="basket-checkout-block basket-checkout-block-btn">
        <button class="black-btn{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                data-entity="basket-checkout-button">
            <?=Loc::getMessage('SBB_ORDER')?>
        </button>
    </div>
</script>