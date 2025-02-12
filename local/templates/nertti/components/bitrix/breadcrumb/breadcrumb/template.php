<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @global CMain $arResult
 */
?>

<?php
$count = count($arResult);
$result = '<div class="breadcrumbs">';
for ($i = 0; $i < $count; $i++) {
    if ($i >= $count - 1) {
        $result .= '<span class="breadcrumbs__item">/</span><span class="breadcrumbs__item">' . $arResult[$i]['TITLE'] . '</span>';
    } else {
        $result .= '<a class="breadcrumbs__item" href="' . $arResult[$i]['LINK'] . '">' . $arResult[$i]['TITLE'] . '</a>';
    }
}
$result .= '</div>';
return $result;
?>
