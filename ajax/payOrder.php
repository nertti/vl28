<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main;

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();
echo '<pre>';
print_r($request);
echo '</pre>';
//file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($_GET, 1), FILE_APPEND);