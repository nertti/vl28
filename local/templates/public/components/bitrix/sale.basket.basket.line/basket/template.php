<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global string $componentPath
 * @global string $templateName
 * @var CBitrixComponentTemplate $this
 */
?>
<?php if ($arResult['NUM_PRODUCTS'] > 0):?>
<span class="header__cart-count"><?=$arResult['NUM_PRODUCTS']?></span>
<?php endif;?>