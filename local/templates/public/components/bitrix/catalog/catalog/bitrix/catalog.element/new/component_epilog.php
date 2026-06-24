<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Context;

$GLOBALS['CATALOG_CURRENT_ELEMENT_INFO'] = [
    'CROSS_SALES_IDS' => array_map('intval', (array)$arResult['PROPERTIES']['CROSS_SALES']['VALUE']),
    'CURRENT_SECTION_ID' => (int)$arResult['IBLOCK_SECTION_ID'],
    'CURRENT_ELEMENT_ID' => (int)$arResult['ID'],
];

$itemId = (int)$arResult['ID'];

// Объявляем глобальную функцию для динамического вычисления класса в кэше
if (!function_exists('getFavoriteClass')) {
    function getFavoriteClass($itemId) {
        global $USER;
        $itemId = (int)$itemId;
        
        if ($itemId <= 0) {
            return '';
        }

        $arFavorites = [];

        // 1. Проверяем избранное для ТЕКУЩЕГО пользователя на этом хите
        if (!$USER->IsAuthorized()) {
            $request = Context::getCurrent()->getRequest();
            $cookieData = $request->getCookie("favorites");
            $arFavorites = !empty($cookieData) ? json_decode($cookieData, true) : [];
            if (!is_array($arFavorites)) {
                $arFavorites = [];
            }
        } else {
            $idUser = (int)$USER->GetID();
            $rsUser = CUser::GetByID($idUser);
            if ($arUser = $rsUser->Fetch()) {
                $arFavorites = $arUser['UF_FAVORITES'];
            }
        }

        // 2. Возвращаем класс активности
        if (is_array($arFavorites) && in_array($itemId, $arFavorites)) {
            return 'active';
        }

        return '';
    }
}