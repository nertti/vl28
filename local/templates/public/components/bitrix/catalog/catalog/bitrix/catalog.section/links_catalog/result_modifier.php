<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (\Bitrix\Main\Loader::includeModule('catalog')) {
    foreach ($arResult["ITEMS"] as $cell => &$arElement) {
        // Получаем оптимальную цену со всеми скидками для текущего пользователя
        $arPrice = CCatalogProduct::GetOptimalPrice(
            $arElement['ID'],
            1,
            $USER->GetUserGroupArray(),
            'N'
        );

        if ($arPrice) {
            // 1. Цена БЕЗ скидки (базовая)
            $arElement['BASE_PRICE'] = $arPrice['RESULT_PRICE']['BASE_PRICE'];

            // 2. Цена СО скидкой (финальная)
            $arElement['DISCOUNT_PRICE'] = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];

            // 3. Форматированные строки для вывода (с валютой)
            $arElement['PRINT_BASE_PRICE'] = CCurrencyLang::CurrencyFormat(
                $arElement['BASE_PRICE'],
                $arPrice['RESULT_PRICE']['CURRENCY'],
                true
            );
            $arElement['PRINT_DISCOUNT_PRICE'] = CCurrencyLang::CurrencyFormat(
                $arElement['DISCOUNT_PRICE'],
                $arPrice['RESULT_PRICE']['CURRENCY'],
                true
            );

            // 4. Флаг наличия скидки (для стилей перечеркнутой цены)
            $arElement['HAS_DISCOUNT'] = $arElement['BASE_PRICE'] > $arElement['DISCOUNT_PRICE'];
        }
    }
    unset($arElement); // Обязательно сбрасываем ссылку после цикла &
}