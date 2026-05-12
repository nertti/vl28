<?php
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

$request = Application::getInstance()->getContext()->getRequest();
$referralLink = $request->get('referral_link');

if (!empty($referralLink)) {
    $cookieTime = time() + 30 * 24 * 60 * 60;
    $cookieValue = json_encode([
        "VALUE" => $referralLink,
        "TIME" => $cookieTime
    ]);

    $cookie = new Cookie("REFERRAL_LINK", $cookieValue, $cookieTime);
    $cookie->setPath("/");
    $cookie->setHttpOnly(false); // если нужно читать через JS

    Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
}
?>