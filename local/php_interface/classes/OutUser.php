<?php

class OutUser
{
    public static function OnAfterUserExit()
    {

        SetCookie(
            "REMEMBER_AUTH",
            '',
            time() - 3600,
            "/",
            "",
        );

    }
}