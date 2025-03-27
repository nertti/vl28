<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["code"])) {
    $code = $_POST["code"];

    if (!isset($_SESSION["SMS_OTP"])) {
        echo json_encode(["success" => false, "error" => "Код не запрашивался"]);
        exit;
    }

    if ($code == $_SESSION["SMS_OTP"]) {
        unset($_SESSION["SMS_OTP"]); // Удаляем код после успешного ввода
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Неверный код"]);
    }
}