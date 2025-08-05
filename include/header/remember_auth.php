<?php
#начало запоминающей авторизации
$cookieValue = $_COOKIE['REMEMBER_AUTH'] ?? null;
if ($cookieValue) {
    $cookieData = json_decode($cookieValue, true);

    // Получаем отдельные значения
    $login = $cookieData['LOGIN'] ?? '';
    $checkword = $cookieData['CHECKWORD'] ?? '';
    $time = $cookieData['TIME'] ?? 0;
}

// Проверка актуальности cookie
if ($time > time()) {
    // Cookie ещё действителен
    // Здесь можно выполнить авторизацию
    $USER->Authorize($cookieData['ID']); // авторизуем
} else {
    // Cookie просрочен
    // Здесь можно удалить cookie
    setcookie('REMEMBER_AUTH', '', time() - 3600, '/');
}
#конец запоминающей авторизации