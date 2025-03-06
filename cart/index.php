<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>

<?php $APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket",
	"",
	Array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"ADDITIONAL_PICT_PROP_5" => "-",
		"AUTO_CALCULATION" => "Y",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"COLUMNS_LIST" => array("NAME","DISCOUNT","WEIGHT","DELETE","DELAY","TYPE","PRICE","QUANTITY"),
		"COLUMNS_LIST_EXT" => array("PREVIEW_PICTURE","DISCOUNT","DELETE","DELAY","TYPE","SUM"),
		"COLUMNS_LIST_MOBILE" => array("PREVIEW_PICTURE","DISCOUNT","DELETE","DELAY","TYPE","SUM"),
		"COMPATIBLE_MODE" => "Y",
		"CORRECT_RATIO" => "Y",
		"DEFERRED_REFRESH" => "N",
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",
		"DISPLAY_MODE" => "compact",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_CONVERT_CURRENCY" => "Y",
		"GIFTS_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_HIDE_NOT_AVAILABLE" => "N",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MESS_BTN_DETAIL" => "Подробнее",
		"GIFTS_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_PLACE" => "BOTTOM",
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_TEXT_LABEL_GIFT" => "Подарок",
		"HIDE_COUPON" => "N",
		"LABEL_PROP" => array("COLLECTION","MATERIAL","SIZE"),
		"LABEL_PROP_MOBILE" => array("COLLECTION","MATERIAL","SIZE"),
		"LABEL_PROP_POSITION" => "top-left",
		"OFFERS_PROPS" => array("SIZES_SHOES","SIZES_CLOTHES"),
		"PATH_TO_ORDER" => "/cart/order/",
		"PRICE_DISPLAY_MODE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_FILTER" => "Y",
		"SHOW_RESTORE" => "Y",
		"TEMPLATE_THEME" => "",
		"TOTAL_BLOCK_DISPLAY" => array("bottom"),
		"USE_DYNAMIC_SCROLL" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_GIFTS" => "Y",
		"USE_PREPAYMENT" => "N",
		"USE_PRICE_ANIMATION" => "Y"
	)
);?><?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>