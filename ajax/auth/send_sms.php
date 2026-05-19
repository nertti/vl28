<?php
session_start();
require_once $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/sms_auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["phone"])) {
    $phone = preg_replace("/[^0-9]/", "", $_POST["phone"]); // Очищаем номер

    if (empty($phone)) {
        echo json_encode(["success" => false, "error" => "Неверный номер"]);
        exit;
    }

    $code = rand(100000, 999999); // Генерация кода
    $_SESSION["SMS_OTP"] = $code; // Сохраняем код в сессии
    $smsSent = sendSms($phone, "Ваш код: $code");
    echo json_encode(["success" => $smsSent, "code" => $code]);
}