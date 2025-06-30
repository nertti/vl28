<?php

/** @var \CMain $APPLICATION */
/** @var \CMain $userBonus */
/** @var $totalPaid */
/** @var $discountPercent */
/** @var $discountCard */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/profile/sale.php");

$APPLICATION->SetTitle("Профиль");

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();
//pr($arUser);


?>
    <div class="container">
        <div class="account__wrap">
            <div class="account__left">
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"profile", 
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "profile",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "N"
	),
	false
); ?>
            </div>
            <div class="account__right">
                <div class="account__default">
                    <div class="account__orders account__orders_border">
                        <p class="account__title">Мои заказы</p>
                        <?php
                        $APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	"order-list", 
	array(
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_P" => "yellow",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"SEF_MODE" => "Y",
		"ORDERS_PER_PAGE" => "5",
		"PATH_TO_PAYMENT" => "payment.php",
		"PATH_TO_BASKET" => "/cart/",
		"SET_TITLE" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"NAV_TEMPLATE" => "",
		"ACTIVE_DATE_FORMAT" => "j F Y",
		"PROP_1" => array(
		),
		"PROP_2" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "C",
			1 => "F",
		),
		"SEF_FOLDER" => "/profile/order-list/",
		"COMPONENT_TEMPLATE" => "order-list",
		"DETAIL_HIDE_USER_INFO" => array(
		),
		"PATH_TO_CATALOG" => "/catalog/",
		"DISALLOW_CANCEL" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array(
			0 => "F",
			1 => "N",
		),
		"REFRESH_PRICES" => "N",
		"ORDER_DEFAULT_SORT" => "ID",
		"ALLOW_INNER" => "N",
		"ONLY_INNER_FULL" => "N",
		"SEF_URL_TEMPLATES" => array(
			"list" => "",
			"detail" => "#ID#/",
			"cancel" => "order_cancel.php?ID=#ID#",
		),
		"VARIABLE_ALIASES" => array(
			"cancel" => array(
				"ID" => "ID",
			),
		)
	),
	false
);?>
                    </div>
                    <div class="account__loyal">
                        <p class="account__title">Карта лояльности</p>
                        <div class="account__loyal-card">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Light">
                            <div class="account__loyal-inner">
                                <p>Уровень карты: <strong><?=$discountCard?></strong></p>
                                <p>Доступные бонусы: <strong><?=$userBonus?> баллов</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="account__data">
                        <p class="account__title">Личные данные</p>
                        <div class="account__data-list">
                            <p><?=$arUser['LAST_NAME']." ".$arUser['NAME']?></p>
                            <p><?=$arUser['PERSONAL_PHONE']?></p>
                            <p><?=$arUser['EMAIL']?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>