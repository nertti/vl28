<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");
/** @var \CMain $APPLICATION */
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
global $USER;
if (!is_object($USER)) $USER = new CUser;
$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
// Проверяем, что форма отправлена
if ($request->isPost()) {

    $email = $request->getPost("email");
    $checkWord = $request->getPost("checkWord");

    $password = $request->getPost("password");
    $confirmPassword = $request->getPost("confirmPassword");

    $errors = [];
    if (empty($checkWord)) {
        $errors['CHECK_WORD'] = "Перейдите по ссылке из сообщения, отправленного на Вашу почту";
    }
    if (empty($password)) {
        $errors['PASSWORD'] = "Поле 'Пароль' обязательно для заполнения";
    }
    if (empty($confirmPassword)) {
        $errors['CONFIRM_PASSWORD'] = "Поле 'Подтвердите пароль' обязательно для заполнения";
    } elseif ($confirmPassword !== $password) {
        $errors['CONFIRM_PASSWORD'] = "Пароли не совпадают";
    }

    // Если есть ошибки, возвращаем их в виде JSON
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errors]);
        exit();
    } else {

        $arResult = $USER->ChangePassword($email, $checkWord, $password, $confirmPassword);
        if($arResult["TYPE"] == "OK") {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Успешная отправка']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Ошибка отправки письма', 'data' => $arResult]);
        }
    }
}