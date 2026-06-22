<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;

global $USER, $APPLICATION;

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

/* --- БЛОК 1. Обработка галереи картинок и видео (Безопасно для кэша) --- */
$arResult['PROCESSED_GALLERY'] = [];
$videoPath = '';

// Получаем путь к видео
if (!empty($arResult['PROPERTIES']['VIDEO']['VALUE'])) {
    $videoPath = CFile::GetPath($arResult['PROPERTIES']['VIDEO']['VALUE']);
}

// Генерируем пути к картинкам заранее
if (!empty($arResult['PROPERTIES']['IMAGES']['VALUE'])) {
    foreach ($arResult['PROPERTIES']['IMAGES']['VALUE'] as $imageId) {
        $originalFile = CFile::GetFileArray($imageId);
        $resizedFile = CFile::ResizeImageGet(
            $imageId,
            ['width' => 760, 'height' => 760],
            BX_RESIZE_IMAGE_EXACT,
            true
        );

        if ($originalFile && $resizedFile) {
            $arResult['PROCESSED_GALLERY'][] = [
                'ORIGINAL_SRC' => $originalFile['SRC'],
                'RESIZED_SRC'  => $resizedFile['src'],
                'WIDTH'        => $resizedFile['width'],
                'HEIGHT'       => $resizedFile['height']
            ];
        }
    }
}
$arResult['VIDEO_PATH_URL'] = $videoPath;


/* --- БЛОК 2. Работа с Highload-блоком цветов --- */
$arResult['OTHER_COLORS'] = array();
$current_color = $arResult['PROPERTIES']['COLOR']['VALUE'];

$hlbl = 2;
$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();
$allColorsData = array();

$rsData = $entity_data_class::getList(array(
    'select' => [
        'UF_COLOR_FILE',
        'UF_NAME',
        'UF_XML_ID'
    ]
));
while ($arData = $rsData->Fetch()) {
    $arData['PICTURE'] = CFile::GetPath($arData['UF_COLOR_FILE']);
    $allColorsData[$arData['UF_XML_ID']] = $arData;

    if ($current_color == $arData['UF_XML_ID']) {
        $arResult['CURRENT_COLOR'] = $arData['UF_NAME'];
        $arResult['CURRENT_COLOR_XML'] = $arData['UF_XML_ID'];
    }
}


/* --- БЛОК 3. Поиск товаров других цветов (MAIN_PRODUCT) --- */
if (!empty($arResult["PROPERTIES"]["MAIN_PRODUCT"]['VALUE'])) {
    $sameSizearSelect = array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "DETAIL_PAGE_URL", "PROPERTY_COLOR");
    $sameSizearFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_ID'], 
        "ACTIVE_DATE" => "Y", 
        "ACTIVE" => "Y", 
        "PROPERTY_MAIN_PRODUCT" => $arResult["PROPERTIES"]["MAIN_PRODUCT"]['VALUE']
    );

    $sameSizeres = CIBlockElement::GetList(array(), $sameSizearFilter, false, array("nPageSize" => 50), $sameSizearSelect);
    while ($sameSizeob = $sameSizeres->GetNext()) {
        $color_name = '';
        $color_xml = '';
        if (isset($allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]])) {
            $color_name = $allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]]['UF_NAME'];
            $color_xml = $allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]]['UF_XML_ID'];
        }
        $arResult['OTHER_COLORS'][] = array(
            "ID" => $sameSizeob["ID"],
            'ANCHOR' => $color_name,
            'LINK' => $sameSizeob["DETAIL_PAGE_URL"],
            "COLOR" => $color_xml,
        );
    }
}


/* --- БЛОК 4. Доп. свойства торговых предложений --- */
if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $offerIndex => $offer) {
        if (!empty($offer['PROPERTIES']['ARTICLE']['VALUE'])) {
            $arResult['JS_OFFERS'][$offerIndex]['PROPERTIES']['ARTICLE'] = array(
                'VALUE' => $offer['PROPERTIES']['ARTICLE']['VALUE']
            );
        }
    }
}


/* --- БЛОК 5. Проверка Избранного --- */
/*
// 1. Получаем массив ID из избранного
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

$arResult['IS_FAVORITE_ITEM'] = false;
if (is_array($arFavorites) && isset($arResult['ID'])) {
    if (in_array($arResult['ID'], $arFavorites)) {
        $arResult['IS_FAVORITE_ITEM'] = true;
    }
}
*/
/* --- БЛОК 6. Сохранение ключей в кэш компонента --- */
$cp = $this->__component; // Объект компонента

if (is_object($cp)) {
    $cp->SetResultCacheKeys(array(
        'PROPERTIES', 
        'IBLOCK_SECTION_ID', 
        'ID',
        'PROCESSED_GALLERY',
        'VIDEO_PATH_URL',
        'OTHER_COLORS',
        'CURRENT_COLOR',
        'CURRENT_COLOR_XML',
        'IS_FAVORITE_ITEM' // Обязательно добавляем сюда избранное!
    ));
}
//pr($arResult, true);