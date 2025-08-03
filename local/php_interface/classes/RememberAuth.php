<?php

class RememberAuth
{
    const COOKIE_LIFETIME = 30 * 24 * 60 * 60; // 30 дней

    public static function OnAfterUserAuthorize($arArgs)
    {
        extract($arArgs);

        if (true)
        {
            $cookieTime = time() + self::COOKIE_LIFETIME;
            $cookieValue = json_encode([
                "LOGIN" => $LOGIN,
                "CHECKWORD" => md5($PASSWORD),
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