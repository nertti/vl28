<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
/** @var array $arParams */
?>
<?php if (!empty($arResult)): ?>
<div class="account__links">
    <?php foreach ($arResult as $arItem):?>
        <div class="account__link-wrap">
            <a href="<?= $arItem["LINK"] ?>" class="account__link <?php if ($arItem['SELECTED']):?>account__link_current<?php endif;?>"><?= $arItem["TEXT"] ?></a>
        </div>
    <?php endforeach ?>
</div>
<?php endif ?>
