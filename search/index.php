<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск по сайту");
?>
<?php $APPLICATION->IncludeComponent(
    "bitrix:search.page",
    "",
    array(
        "RESTART" => "Y",
        "CHECK_DATES" => "Y",
        "arrWHERE" => array(
            0 => "forum",
            1 => "blog",
        ),
        "arrFILTER" => array(
            0 => "main",
            1 => "iblock_main_ru",
        ),
        "SHOW_WHERE" => "N",
        "PAGE_RESULT_COUNT" => "50",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
        "TAGS_SORT" => "NAME",
        "TAGS_PAGE_ELEMENTS" => "20",
        "TAGS_PERIOD" => "",
        "TAGS_URL_SEARCH" => "",
        "TAGS_INHERIT" => "Y",
        "SHOW_RATING" => "Y",
        "FONT_MAX" => "50",
        "FONT_MIN" => "10",
        "COLOR_NEW" => "000000",
        "COLOR_OLD" => "C8C8C8",
        "PERIOD_NEW_TAGS" => "",
        "SHOW_CHAIN" => "Y",
        "COLOR_TYPE" => "Y",
        "WIDTH" => "100%",
        "PATH_TO_USER_PROFILE" => "#SITE_DIR#people/user/#USER_ID#/",
        "COMPONENT_TEMPLATE" => "main-search",
        "NO_WORD_LOGIC" => "N",
        "USE_TITLE_RANK" => "Y",
        "DEFAULT_SORT" => "rank",
        "FILTER_NAME" => "",
        "SHOW_WHEN" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "USE_LANGUAGE_GUESS" => "N",
        "USE_SUGGEST" => "Y",
        "SHOW_ITEM_TAGS" => "Y",
        "SHOW_ITEM_DATE_CHANGE" => "Y",
        "SHOW_ORDER_BY" => "Y",
        "SHOW_TAGS_CLOUD" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Результаты поиска",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => "",
        "arrFILTER_main" => array(
        ),
        "arrFILTER_iblock_main_ru" => array(
            0 => "2",
            1 => "3",
            2 => "4",
            3 => "5",
            4 => "7",
            5 => "8",
            6 => "9",
            7 => "10",
            8 => "11",
            9 => "12",
            10 => "13",
            11 => "14",
            12 => "15",
            13 => "16",
        ),
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO"
    ),
    false
); ?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>