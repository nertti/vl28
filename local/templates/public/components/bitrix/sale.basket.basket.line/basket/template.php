<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @global string $componentPath
 * @global string $templateName
 * @var CBitrixComponentTemplate $this
 */

?>
<?php if ($arResult['BASKET'] > 0):?>
<span class="header__cart-count"><?=$arResult['BASKET']?></span>
<?php endif;?>