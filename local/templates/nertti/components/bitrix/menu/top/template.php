<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
/** @var array $arParams */
?>
<?php if (!empty($arResult)): ?>

    <?php
    foreach ($arResult as $arItem):
        if ($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
            continue;
        ?>
        <?php if ($arItem["SELECTED"]): ?>
        <li class="h2 selected"><a href="<?= $arItem["LINK"] ?>" class="selected"><?= $arItem["TEXT"] ?></a></li>
    <?php else: ?>
        <li class="h2"><a href="<?= $arItem["LINK"] ?>"><?= $arItem["TEXT"] ?></a></li>
    <?php endif ?>

    <?php endforeach ?>

<?php endif ?>