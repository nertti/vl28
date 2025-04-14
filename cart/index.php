<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>

<?php $APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "cart", Array(
	"ACTION_VARIABLE" => "action",	// Название переменной действия
		"ADDITIONAL_PICT_PROP_2" => "-",	// Дополнительная картинка [Каталог]
		"ADDITIONAL_PICT_PROP_5" => "-",	// Дополнительная картинка [Торговые предложения]
		"AUTO_CALCULATION" => "Y",	// Автопересчет корзины
		"BASKET_IMAGES_SCALING" => "adaptive",	// Режим отображения изображений товаров
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "WEIGHT",
			3 => "DELETE",
			4 => "DELAY",
			5 => "TYPE",
			6 => "PRICE",
			7 => "QUANTITY",
		),
		"COLUMNS_LIST_EXT" => array(	// Выводимые колонки
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "DELAY",
			4 => "TYPE",
			5 => "SUM",
		),
		"COLUMNS_LIST_MOBILE" => array(	// Колонки, отображаемые на мобильных устройствах
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "DELAY",
			4 => "TYPE",
			5 => "SUM",
		),
		"COMPATIBLE_MODE" => "Y",	// Включить режим совместимости
		"CORRECT_RATIO" => "Y",	// Автоматически рассчитывать количество товара кратное коэффициенту
		"DEFERRED_REFRESH" => "N",	// Использовать механизм отложенной актуализации данных товаров с провайдером
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",	// Расположение процента скидки
		"DISPLAY_MODE" => "compact",	// Режим отображения корзины
		"EMPTY_BASKET_HINT_PATH" => "/",	// Путь к странице для продолжения покупок
		"GIFTS_BLOCK_TITLE" => "Выберите один из подарков",	// Текст заголовка "Подарки"
		"GIFTS_CONVERT_CURRENCY" => "Y",	// Показывать цены в одной валюте
		"GIFTS_HIDE_BLOCK_TITLE" => "N",	// Скрыть заголовок "Подарки"
		"GIFTS_HIDE_NOT_AVAILABLE" => "N",	// Не отображать товары, которых нет на складах
		"GIFTS_MESS_BTN_BUY" => "Выбрать",	// Текст кнопки "Выбрать"
		"GIFTS_MESS_BTN_DETAIL" => "Подробнее",	// Текст кнопки "Подробнее"
		"GIFTS_PAGE_ELEMENT_COUNT" => "4",	// Количество элементов в строке
		"GIFTS_PLACE" => "BOTTOM",	// Вывод блока "Подарки"
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "",	// Название переменной, в которой передается количество товара
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",	// Показывать процент скидки
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",	// Показывать старую цену
		"GIFTS_TEXT_LABEL_GIFT" => "Подарок",	// Текст метки "Подарка"
		"HIDE_COUPON" => "N",	// Спрятать поле ввода купона
		"LABEL_PROP" => array(	// Свойства меток товара
			0 => "COLLECTION",
			1 => "MATERIAL",
			2 => "SIZE",
		),
		"LABEL_PROP_MOBILE" => array(	// Свойства меток товара, отображаемые на мобильных устройствах
			0 => "COLLECTION",
			1 => "MATERIAL",
			2 => "SIZE",
		),
		"LABEL_PROP_POSITION" => "top-left",	// Расположение меток товара
		"OFFERS_PROPS" => array(
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
		),
		"PATH_TO_ORDER" => "/cart/order/",	// Страница оформления заказа
		"PRICE_DISPLAY_MODE" => "Y",	// Отображать цену в отдельной колонке
		"PRICE_VAT_SHOW_VALUE" => "N",	// Отображать значение НДС
		"PRODUCT_BLOCKS_ORDER" => "props,sku,columns",	// Порядок отображения блоков товара
		"QUANTITY_FLOAT" => "N",	// Использовать дробное значение количества
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"SHOW_DISCOUNT_PERCENT" => "Y",	// Показывать процент скидки рядом с изображением
		"SHOW_FILTER" => "N",	// Отображать фильтр товаров
		"SHOW_RESTORE" => "N",	// Разрешить восстановление удалённых товаров
		"TEMPLATE_THEME" => "",	// Цветовая тема
		"TOTAL_BLOCK_DISPLAY" => array(	// Отображение блока с общей информацией по корзине
			0 => "bottom",
		),
		"USE_DYNAMIC_SCROLL" => "Y",	// Использовать динамическую подгрузку товаров
		"USE_ENHANCED_ECOMMERCE" => "N",	// Отправлять данные электронной торговли в Google и Яндекс
		"USE_GIFTS" => "Y",	// Показывать блок "Подарки"
		"USE_PREPAYMENT" => "N",	// Использовать предавторизацию для оформления заказа (PayPal Express Checkout)
		"USE_PRICE_ANIMATION" => "Y",	// Использовать анимацию цен
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>