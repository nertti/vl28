<?php

class RememberAuth
{
    const COOKIE_LIFETIME = 30 * 24 * 60 * 60; // 30 дней

    public static function OnAfterUserAuthorize($arArgs)
    {
        extract($arArgs['user_fields']);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/log.txt', print_r($ID, 1), FILE_APPEND);

        if (true)
        {
            $cookieTime = time() + self::COOKIE_LIFETIME;
            $cookieValue = json_encode([
                "ID" => $ID,
                "TIME" => $cookieTime
            ]);
            SetCookie(
                "REMEMBER_AUTH",
                $cookieValue,
                $cookieTime,
                "/",
                "",
            );
        } else {
            SetCookie(
                "REMEMBER_AUTH",
                '',
                time() - 3600,
                "/",
                "",
            );
        }
    }
}