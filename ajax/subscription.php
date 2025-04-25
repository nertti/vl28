<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Логируем входные данные
//file_put_contents("error.log", $_POST, FILE_APPEND);

use Bitrix\Main\Loader;

// Подключаем модули
Loader::includeModule('subscribe'); // Подключаем модуль Подписка, рассылки
Loader::includeModule('mail'); // Подключаем модуль Письма

$pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

// Проверяем заполненные значения
$errors = [];
if (empty($_POST['EMAIL'])) {
    $errors['EMAIL'] = "Пожалуйста, введите свой email.";
} elseif (!preg_match($pattern, $_POST['EMAIL'])) {
    $errors['EMAIL'] = "Введите корректный электронный адрес";
}

// Если есть ошибки, возвращаем их в виде JSON
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
    exit();
}

global $USER;
$email = $_POST['EMAIL'];
$subscribeFields = [
    'USER_ID' => ($USER->IsAuthorized() ? $USER->GetID() : false),
    'FORMAT' => 'html',
    'EMAIL' => $email,
    'ACTIVE' => 'Y',
    'CONFIRMED' => 'Y', // Подтверждаем подписку без подтверждения по почте
    'SEND_CONFIRM' => 'N', // Не отправляем письмо с подтверждение подписчику
    'RUB_ID' => [1] // Указываем ID инфоблока, например у моих новостей ID == 1
];

$subscr = new CSubscription;
$ID = $subscr->Add($subscribeFields);
if ($ID) {
    $date = new DateTime();
    $format = $date->format("d.m.Y");
    $EVENT_TYPE = 'SUBSCRIBE_CONFIRM';
    $arFeedForm = array(
        "ID" => htmlspecialchars($ID),
        "EMAIL" => htmlspecialchars($_POST['EMAIL']),
        "DATE_SUBSCR" => htmlspecialchars($format),
        "TEXT" => 'Ваши данные успешно отправлены',
    );
    CEvent::Send($EVENT_TYPE, "s1", $arFeedForm);

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Спасибо за обратную связь! Ваше сообщение было успешно отправлено.']);
} else {
    $errors['SUB'] = "Вы уже подписаны.";
}


// Выводим ошибки, если они есть
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $errors]);
}
