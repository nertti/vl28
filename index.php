<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Главная");
?>
<?php include $_SERVER['DOCUMENT_ROOT'].'/indexblocks.php'; ?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>