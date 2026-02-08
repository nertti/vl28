<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaid');
$eventManager->addEventHandler('sale', 'OnOrderAdd', 'onOrderPaid');
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'onOrderCreate');
$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaidHandler');

// после регистрации считаем какая карта лояльности и скидка
$eventManager->addEventHandler("main", "OnAfterUserRegister", "onAfterUserUpdateHandler");
// при изменении свойств карт лояльности пересчитываем карты у пользователей
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnLoyaltyCardChanged");

AddEventHandler("main", "OnAfterUserAuthorize", Array("RememberAuth", "OnAfterUserAuthorize"));
AddEventHandler("main", "OnUserLogout", Array("OutUser", "OnAfterUserExit"));

use Bitrix\Main\EventManager;

$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', 'sendNewsToSubscribers');
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', 'sendNewsToSubscribers');