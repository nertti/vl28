<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('sale', 'OnOrderUpdate', 'onOrderPaid');
AddEventHandler("main", "OnAfterUserAuthorize", Array("RememberAuth", "OnAfterUserAuthorize"));