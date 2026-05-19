<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");


use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

$request = Application::getInstance()->getContext()->getRequest();
$cookieValue = $request->getCookie("UTM");

if (!empty($cookieValue)) {
    try {
        $data = Json::decode($cookieValue);

        if (isset($data['UF_UTM_PARTNER'])) {
            $utmPartner = $data['UF_UTM_PARTNER'] ?? null;
        }
        if (isset($data['UF_UTM_CAMPAIGN'])) {
            $utmCampaign = $data['UF_UTM_CAMPAIGN'] ?? null;
        }
        if (isset($data['UF_UTM_SOURCE'])) {
            $utmSource = $data['UF_UTM_SOURCE'] ?? null;
        }
    } catch (\Exception $e) {}
}

?>
<section class="registration">
    <div class="container">
        <p class="h2">Регистрация</p>

        <form action="/ajax/auth/signin.php" class="registration__form" id="auth_form">
            <input type="hidden" id="UF_UTM_SOURCE" name="utmSource" value="<?=$utmSource?>">
            <input type="hidden" id="UF_UTM_CAMPAIGN" name="utmCampaign" value="<?=$utmCampaign?>">
            <input type="hidden" id="UF_UTM_PARTNER" name="utmPartner" value="<?=$utmPartner?>">

            <input type="text" class="form-input" name="email" id="email" placeholder="E-mail">
            <input type="text" class="form-input phone-input" name="phone" id="phone-number" placeholder="Телефон" style="margin-top: 10px">
            <input type="password" class="form-input" name="password" id="password" placeholder="Пароль" style="margin-top: 10px">
            <input type="password" class="form-input" name="confirm_password" id="confirm_password" placeholder="Подтвердите пароль" style="margin-top: 10px">
            <button type="submit" class="registration__btn">Зарегистрироваться</button>
            <p class="registration__desc" id="sms-message-desk">
                Уже есть аккаунт?
                <a href="/signin/">
                    Авторизоваться
                </a>
            </p>
        </form>

    </div>
</section>
<div class="hystmodal" id="authModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="min-height: auto; width: unset; padding: 35px 75px 25px;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <div class="h2" style="margin-bottom: 20px">Ошибка</div>
                <div class="gray" style="margin-bottom: 10px"></div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let authModal = new HystModal({linkAttributeName: 'data-hystmodal',});
        let form = document.getElementById("auth_form")
        // Форма отправки номера телефона
        form.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(form);

            fetch("/ajax/auth/signup.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        let errorMessages = [];

                        if (data.message.PASSWORD) {
                            errorMessages.push(data.message.PASSWORD);
                        }
                        if (data.message.CONFIRM_PASSWORD) {
                            errorMessages.push(data.message.CONFIRM_PASSWORD);
                        }
                        if (data.message.EMAIL) {
                            errorMessages.push(data.message.EMAIL);
                        }
                        if (errorMessages.length > 0) {
                            document.querySelector('#authModal .alertText .gray').innerHTML = errorMessages.join('<br>');
                            authModal.open('#authModal');
                        }
                    } else {
                        window.location.href = '/profile/';
                    }
                });
        });

    });
</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
