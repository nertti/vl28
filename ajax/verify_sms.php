<?php
session_start();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["code"])) {
    $code = $_POST["code"];
    $phone = preg_replace("/[^0-9]/", "", $_POST["phone"]); // Очищаем номер

    global $USER;
    if (!isset($_SESSION["SMS_OTP"])) {
        echo json_encode(["success" => false, "error" => "Код не запрашивался"]);
        exit;
    }

    if ($code == $_SESSION["SMS_OTP"]) {
        unset($_SESSION["SMS_OTP"]); // Удаляем код после успешного ввода
        $rsUsers = CUser::GetList(array(), 'sort', array('PERSONAL_PHONE' => $phone));
        if ($rsUsers->SelectedRowsCount() <= 0) {
            $arResult = $USER->Register($phone, "", "", $phone, $phone, $phone . "@vl28.ru");
            if ($arResult['TYPE'] == 'OK') {
                $user = new CUser;
                $fields = array(
                    "PERSONAL_PHONE" => $phone,
                );
                $user->Update($arResult['ID'], $fields);
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => $arResult, 'test' => $rsUsers->SelectedRowsCount()]);
            }
        } else {
            $rsUser = CUser::GetByLogin($phone);
            $arUser = $rsUser->Fetch();
            $USER->Authorize($arUser['ID']); // авторизуем
            echo json_encode(["success" => true, 'id' => $arUser['ID']]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Неверный код"]);
    }
}