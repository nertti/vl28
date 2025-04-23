<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/** @var CUser $USER */
$GLOBALS['APPLICATION']->RestartBuffer();

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

$application = Application::getInstance();
$context = $application->getContext();

/* Избранное */

global $APPLICATION;
$result = 0;
if ($_POST['id']) {
    if (!$USER->IsAuthorized()) // Для неавторизованного
    {
        $arElements = unserialize($APPLICATION->get_cookie('favorites'));
        if (empty($arElements)){
            $arElements = array();
        }
        if (!in_array($_POST['id'], $arElements)) {
            $arElements[] = $_POST['id'];
            $result = 1; // Датчик. Добавляем
        } else {
            $key = array_search($_POST['id'], $arElements); // Находим элемент, который нужно удалить из избранного
            unset($arElements[$key]);
            $result = 2; // Датчик. Удаляем
        }
        setcookie("BITRIX_SM_favorites", serialize($arElements), time() + 60 * 60 * 24 * 60, "/", "vl26908655.nichost.ru");
//        $cookie = new Cookie("favorites", serialize($arElements), time() + 60 * 60 * 24 * 60);
//        $cookie->setDomain($context->getServer()->getHttpHost());
//        $cookie->setHttpOnly(false);
//        $context->getResponse()->addCookie($cookie);
//        $context->getResponse()->flush();
        //$context->getResponse()->send(); // Или просто вернуть $response
    } else { // Для авторизованного
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arElements = $arUser['UF_FAVORITES'];  // Достаём избранное пользователя
        if (!in_array($_POST['id'], $arElements)) // Если еще нету этой позиции в избранном
        {
            $arElements[] = $_POST['id'];
            $result = 1;
        } else {
            $key = array_search($_GET['id'], $arElements); // Находим элемент, который нужно удалить из избранного
            unset($arElements[$key]);
            $result = 2;
        }
        $USER->Update($idUser, array("UF_FAVORITES" => $arElements)); // Добавляем элемент в избранное
    }
}

/* Избранное */
echo json_encode($result);
die();