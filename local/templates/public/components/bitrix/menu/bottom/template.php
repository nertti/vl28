<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
/** @var array $arParams */
?>
<?php if (!empty($arResult)): ?>
    <?php foreach ($arResult as $arItem):?>
    <?php
        if(str_contains($arItem["LINK"], 'tel')) {
            $arItem["LINK"] = str_replace('/', '', $arItem["LINK"]);
        }
    ?>
        <li class="footer__item">
            <a href="<?= $arItem["LINK"] ?>" class="footer__link" <?php if ($arItem["PARAMS"]['data-hystmodal']): ?> data-hystmodal="#<?=$arItem["PARAMS"]['data-hystmodal']?>"<?php endif; ?>><?= $arItem["TEXT"] ?></a>
            <?php if ($arItem["PARAMS"]['description']): ?>
                <span class="footer__subtext"><?= $arItem["PARAMS"]['description'] ?></span>
            <?php endif; ?>
        </li>
    <?php endforeach ?>
<?php endif ?>