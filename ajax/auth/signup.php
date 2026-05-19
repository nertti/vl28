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
    $phone = $request->getPost("phone");
    $password = $request->getPost("password");
    $confirmPassword = $request->getPost("confirm_password");

    $utmSource = $request->getPost("utmSource");
    $utmCampaign = $request->getPost("utmCampaign");
    $utmPartner = $request->getPost("utmPartner");
    // Проверяем заполненные значения
    $errors = [];
    if (empty($email)) {
        $errors['EMAIL'] = "Поле 'E-mail' обязательно для заполнения";
    }  elseif (!preg_match($pattern, $email)) {
        $errors['EMAIL'] = "Введите корректный электронный адрес";
    } else {
        $rsUsers = CUser::GetList(array(), 'sort', array('LOGIN' => $email));
        if ($rsUsers->SelectedRowsCount() > 0){
            $errors['EMAIL'] = "Данный электронный адрес зарегистрирован";
            //$errors['DATA'] = $rsUsers->SelectedRowsCount();
        }
    }
    if (empty($password)) {
        $errors['PASSWORD'] = "Поле 'Пароль' обязательно для заполнения";
    }
    if (empty($confirmPassword)) {
        $errors['CONFIRM_PASSWORD'] = "Поле 'Подтвердить пароль' обязательно для заполнения";
    } elseif ($confirmPassword !== $password) {
        $errors['CONFIRM_PASSWORD'] = "Пароли не совпадают";
    }

    // Если есть ошибки, возвращаем их в виде JSON
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errors]);
        exit();
    } else {
        $arResult = $USER->Register($email, "", "", $password, $confirmPassword, $email);

        $result = $USER->Update($USER->GetID(), array(
            'UF_UTM_SOURCE' => $utmSource ?? null,
            'UF_UTM_CAMPAIGN' => $utmCampaign ?? null,
            'UF_UTM_PARTNER' => $utmPartner ?? null,
            'PERSONAL_PHONE' => $phone,
        ));

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Успешная регистрация']);
    }
}