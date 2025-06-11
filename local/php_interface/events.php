<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler('sale', 'OnSalePayOrder', ['OrderHandler', 'onOrderPaid']);