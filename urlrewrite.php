<?php
$arUrlRewrite=array (
  20 => 
  array (
    'CONDITION' => '#^/profile/orders-history/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/profile/orders-history/index.php',
    'SORT' => 100,
  ),
  15 => 
  array (
    'CONDITION' => '#^/profile/order-list/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/profile/order-list/index.php',
    'SORT' => 100,
  ),
  21 => 
  array (
    'CONDITION' => '#^/profile/orders/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/profile/orders/index.php',
    'SORT' => 100,
  ),
  17 => 
  array (
    'CONDITION' => '#^([^/]+?)\\??(.*)#',
    'RULE' => 'SECTION_CODE=$1&$2',
    'ID' => 'bitrix:catalog.section',
    'PATH' => '/local/templates/public/components/bitrix/catalog.element/.default/template.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
);
