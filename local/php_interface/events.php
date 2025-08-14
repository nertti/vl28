<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaid');
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'onOrderCreate');
AddEventHandler("main", "OnAfterUserAuthorize", Array("RememberAuth", "OnAfterUserAuthorize"));
AddEventHandler("main", "OnUserLogout", Array("OutUser", "OnAfterUserExit"));