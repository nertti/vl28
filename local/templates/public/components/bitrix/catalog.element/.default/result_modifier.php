<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock;
use Bitrix\Highloadblock as HL;

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();


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


if (!empty($arResult["PROPERTIES"]["MAIN_PRODUCT"]['VALUE'])) {


    $sameSizearSelect = array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "DETAIL_PAGE_URL", "PROPERTY_COLOR");
    $sameSizearFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "PROPERTY_MAIN_PRODUCT" => $arResult["PROPERTIES"]["MAIN_PRODUCT"]['VALUE']);

    $sameSizeres = CIBlockElement::GetList(array(), $sameSizearFilter, false, array("nPageSize" => 50), $sameSizearSelect);
    while ($sameSizeob = $sameSizeres->GetNext()) {
        $color_name = '';
        $color_image = '';
        if (isset($allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]])) {
            $color_name = $allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]]['UF_NAME'];
            //$color_image = $allColorsData[$sameSizeob["PROPERTY_COLOR_VALUE"]]['PICTURE'];
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
if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as $offerIndex => $offer) {
        if (!empty($offer['PROPERTIES']['ARTICLE']['VALUE'])) {
            $arResult['JS_OFFERS'][$offerIndex]['PROPERTIES']['ARTICLE'] = array(
                'VALUE' => $offer['PROPERTIES']['ARTICLE']['VALUE']
            );
        }
    }
}
