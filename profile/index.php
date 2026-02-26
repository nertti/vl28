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
                    <div class="account__loyal">
                        <p class="account__title">Карта лояльности</p>
                        <a href="#" data-hystmodal="#loyalModal" data-type="<?= $discountCard ?>" class="account__loyal-card">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Light">
                            <div class="account__loyal-inner">
                                <p>Уровень карты: <strong><?=$discountCard?></strong></p>
                                <p>Доступные бонусы: <strong><?=$userBonus?> баллов</strong></p>
                            </div>
                        </a>
                    </div>
                    <div class="account__data">
                        <p class="account__title">Личные данные</p>
                        <div class="account__data-list">
                            <p><?=$arUser['LAST_NAME']." ".$arUser['NAME']?></p>
                            <p><?=$arUser['PERSONAL_PHONE']?></p>
                            <p><?= substr_compare($arUser['EMAIL'], '@vl28.ru', -strlen('@vl28.ru')) ? $arUser['EMAIL'] : ''; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cardImg = document.querySelector('.account__loyal-card');
            cardImg.addEventListener('click', handleCardClick);
            let loyalItem = document.querySelectorAll('.loyal__nav-item');

            function handleCardClick(event) {
                event.preventDefault();

                let cardType = cardImg.dataset.type;

                // Определяем номер таба на основе типа карты
                let tabIndex;
                switch(cardType) {
                    case 'Light':
                        tabIndex = 0;
                        break;
                    case 'Highlight':
                        tabIndex = 1;
                        break;
                    case 'Luxury':
                        tabIndex = 2;
                        break;
                    default:
                        return;
                }

                // Удаляем активный класс со всех табов
                loyalItem.forEach(item => {
                    item.classList.remove('active');
                });

                // Добавляем активный класс к нужному табу
                loyalItem[tabIndex].classList.add('active');
            }
        });
    </script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>