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
            echo json_encode(["success" => false, "error" => 'Вы не зарегистрированы либо проходили регистрацию через электронную почту']);
        } else {
            $rsUser = CUser::GetByLogin($phone);
            $arUser = $rsUser->Fetch();
            //$USER->Authorize($arUser['ID']); // авторизуем
            if ($arUser['UF_OLD_AUTH'] == 1){
                echo json_encode(["success" => true, 'id' => $arUser['ID'], 'data' => $arUser]);
            }else{
                echo json_encode(["success" => false, 'id' => $arUser['ID'], 'error' => 'Вам нужно восстановить пароль через форму <a href="/recovery-password/">Забыл пароль</a>']);
            }
        }
    } else {
        echo json_encode(["success" => false, "error" => "Неверный код"]);
    }
}