<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Проверяем, что форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    $errors = [];
    $arFields = [];
    if (empty($_POST['NAME']) && isset($_POST['NAME'])) {
        $errors['NAME'] = "Пожалуйста, введите свое имя.";
    } elseif (!empty($_POST['NAME'])) {
        $arFields['NAME'] = htmlspecialchars($_POST['NAME']);
    }
    if (empty($_POST['LAST_NAME']) && isset($_POST['LAST_NAME'])) {
        $errors['LAST_NAME'] = "Пожалуйста, введите свою фамилию.";
    } elseif (!empty($_POST['LAST_NAME'])) {
        $arFields['LAST_NAME'] = htmlspecialchars($_POST['LAST_NAME']);
    }
    if (empty($_POST['PHONE']) && isset($_POST['PHONE'])) {
        $errors['PHONE'] = "Пожалуйста, введите свой телефон.";
    } elseif (!empty($_POST['PHONE'])) {
        $arFields['PERSONAL_PHONE'] = htmlspecialchars($_POST['PHONE']);
    }
    if (empty($_POST['GENDER']) && isset($_POST['GENDER'])) {
        $errors['GENDER'] = "Пожалуйста, введите свой пол.";
    } elseif (!empty($_POST['GENDER'])) {
        $arFields['PERSONAL_GENDER'] = htmlspecialchars($_POST['GENDER']);
    }
    if (empty($_POST['BIRTHDAY']) && isset($_POST['BIRTHDAY'])) {
        $errors['BIRTHDAY'] = "Пожалуйста, введите свою дату рождения.";
    } elseif (!empty($_POST['BIRTHDAY'])) {
        $arFields['PERSONAL_BIRTHDAY'] = htmlspecialchars($_POST['BIRTHDAY']);
    }
    if (empty($_POST['PHONE']) && isset($_POST['PHONE']) || $_POST['PHONE'] === '+375(__)___-__-__') {
        $errors['PHONE'] = "Пожалуйста, введите свой мобильный телефон.";
    } elseif (!empty($_POST['PHONE'])) {
        $arFields['PERSONAL_PHONE'] = htmlspecialchars($_POST['PHONE']);
    }
    if (empty($_POST['SIZE']) && isset($_POST['SIZE'])) {
        $errors['SIZE'] = "Пожалуйста, введите свой размер одежды.";
    } elseif (!empty($_POST['SIZE'])) {
        $arFields['UF_SIZE'] = htmlspecialchars($_POST['SIZE']);
    }
    if (empty($_POST['EMAIL'])) {
        $errors['EMAIL'] = "Пожалуйста, введите свой email.";
    } elseif (!preg_match($pattern, $_POST['EMAIL'])) {
        $errors['EMAIL'] = "Введите корректный электронный адрес";
    } else {
        $arFields['EMAIL'] = htmlspecialchars($_POST['EMAIL']);
    }

    // Если есть ошибки, возвращаем их в виде JSON
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errors]);
        exit();
    }
    // Сохраняем данные в Bitrix
    $user = new CUser();
    $result = $user->Update($_POST['ID'], $arFields);
    if ($result) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Ваши данные успешно обновлены.', 'data' => $arFields]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $user->LAST_ERROR]);
    }
}