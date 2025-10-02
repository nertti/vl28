<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaid');
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'onOrderCreate');
$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaidHandler');

// если пользователю подтвердили заявку на вступление в программу лояльности
$eventManager->addEventHandler("main", "OnAfterUserUpdate", "onAfterUserUpdateHandler");
AddEventHandler("main", "OnAfterUserAuthorize", Array("RememberAuth", "OnAfterUserAuthorize"));
AddEventHandler("main", "OnUserLogout", Array("OutUser", "OnAfterUserExit"));