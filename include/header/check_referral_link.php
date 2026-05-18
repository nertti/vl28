<?php
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

$request = Application::getInstance()->getContext()->getRequest();
$utmSource = $request->get('utm_source');
$utmCampaign = $request->get('utm_campaign');
$utmPartner = $request->get('utm_partner');

if (!empty($utmSource)) {
    $cookieTime = time() + 30 * 24 * 60 * 60;
    $cookieValue = json_encode([
        "UF_UTM_SOURCE" => $utmSource,
        "UF_UTM_CAMPAIGN" => $utmCampaign,
        "UF_UTM_PARTNER" => $utmPartner,
        "TIME" => $cookieTime
    ]);

    $cookie = new Cookie("UTM", $cookieValue, $cookieTime);
    $cookie->setPath("/");
    $cookie->setHttpOnly(false); // если нужно читать через JS

    Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
}
?>