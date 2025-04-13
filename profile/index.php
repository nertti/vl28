<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
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
                        <p>Активных заказов пока нет</p>
                    </div>
                    <div class="account__loyal">
                        <p class="account__title">Карта лояльности</p>
                        <div class="account__loyal-card">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Light">
                            <div class="account__loyal-inner">
                                <p>Уровень карты: <strong>Light</strong></p>
                                <p>Доступные бонусы: <strong>698 баллов</strong></p>
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