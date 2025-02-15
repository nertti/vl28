<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
/** @var array $arParams */
?>
<?php if (!empty($arResult)): ?>
    <?php foreach ($arResult as $arItem):?>
        <li class="footer__item">
            <a href="<?= $arItem["LINK"] ?>" class="footer__link"><?= $arItem["TEXT"] ?></a>
            <?php if ($arItem["PARAMS"]['description']): ?>
                <span class="footer__subtext"><?= $arItem["PARAMS"]['description'] ?></span>
            <?php endif; ?>
        </li>
    <?php endforeach ?>
<?php endif ?>