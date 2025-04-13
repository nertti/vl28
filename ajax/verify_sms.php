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
        $arResult = $USER->Register($phone, "", "", $phone, $phone, $phone."@vl28.ru");
        if ($arResult['TYPE'] == 'OK'){
            $user = new CUser;
            $fields = Array(
                "PERSONAL_PHONE" => $phone,
            );
            $user->Update($arResult['ID'], $fields);
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $arResult]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Неверный код"]);
    }
}