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

    $errors = [];
    if (empty($email)) {
        $errors['EMAIL'] = "Поле 'E-mail' обязательно для заполнения";
    } elseif (!preg_match($pattern, $email)) {
        $errors['EMAIL'] = "Введите корректный электронный адрес";
    } else {
        $rsUsers = CUser::GetList(array(), 'sort', array('EMAIL' => $email));
        if ($rsUsers->SelectedRowsCount() <= 0) {
            $errors['EMAIL'] = "Данный электронный адрес не зарегистрирован";
        }
    }

    // Если есть ошибки, возвращаем их в виде JSON
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errors]);
        exit();
    } else {

        $result = $USER->SendPassword($email, $email, 's1');
        if ($result['TYPE'] == 'OK') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Успешная отправка']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Ошибка отправки письма', 'data' => $result]);
        }
    }
}