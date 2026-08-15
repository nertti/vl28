<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
echo "Отправка почты...<br>";
CEvent::CheckEvents();
echo "Готово!";
?>