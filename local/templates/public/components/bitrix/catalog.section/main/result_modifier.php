<?php
$crossSalesIds = array_map(
    'intval',
    (array)$GLOBALS['CROSS_SALES_IDS']
);

$currentSectionId = (int)$GLOBALS['CURRENT_SECTION_ID'];

$crossItems = [];
$sectionItems = [];
$otherItems = [];

// для быстрого поиска
$crossSalesMap = array_flip($crossSalesIds);

foreach ($arResult['ITEMS'] as $item)
{
    $itemId = (int)$item['ID'];

    if (isset($crossSalesMap[$itemId]))
    {
        $crossItems[$itemId] = $item;
        continue;
    }

    if ((int)$item['IBLOCK_SECTION_ID'] === $currentSectionId)
    {
        $sectionItems[] = $item;
    }
    else
    {
        $otherItems[] = $item;
    }
}

// сохраняем порядок из свойства CROSS_SALES
$sortedCrossItems = [];

foreach ($crossSalesIds as $id)
{
    if (isset($crossItems[$id]))
    {
        $sortedCrossItems[] = $crossItems[$id];
    }
}

$arResult['ITEMS'] = array_slice(
    array_merge(
        $sortedCrossItems,
        $sectionItems,
        $otherItems
    ),
    0,
    4
);