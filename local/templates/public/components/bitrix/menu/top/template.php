<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
/** @var array $arParams */
?>
<?php if (!empty($arResult)): ?>
    <?php foreach ($arResult as $arItem):?>
        <li class="h3"><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a></li>
    <?php endforeach ?>
<?php endif ?>