<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Вход или регистрация");

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

header("Location: /signin/");
exit( );

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
        <p class="h2">Вход или регистрация</p>

        <!-- Форма ввода номера телефона -->
        <form action="#" class="registration__form" id="phone-form">
            <input type="text" class="form-input phone-input" id="phone-number" placeholder="+7">
            <input type="submit" class="registration__btn" value="Далее">
        </form>

        <!-- Сообщение о вводе кода (скрыто до отправки SMS) -->
        <p class="registration__desc" id="sms-message-desk" style="display: none;">
            Продолжая, вы даете <a href="https://vl28.pro/personal/">согласие</a> на обработку персональных данных.
        </p>
        <br>
        <p class="registration__desc" id="sms-message" style="display: none;">
            Введите код, который мы выслали на номер <span id="user-phone"></span>
        </p>

        <!-- Форма ввода кода (скрыта до отправки SMS) -->
        <form action="#" class="registration__form" id="code-form" style="display: none;">
            <input type="hidden" class="form-input phone-input" id="phone-number-send" placeholder="+7">
            <input type="hidden" id="UF_UTM_SOURCE" value="<?=$utmSource?>">
            <input type="hidden" id="UF_UTM_CAMPAIGN" value="<?=$utmCampaign?>">
            <input type="hidden" id="UF_UTM_PARTNER" value="<?=$utmPartner?>">
            <input type="text" class="registration__code" id="sms-code" placeholder="Код из СМС">
            <input type="submit" class="registration__btn" value="Подтвердить">
        </form>

        <!-- Таймер повторной отправки -->
        <p class="registration__desc top40" id="resend-timer" style="display: none;">
            Повторная отправка через <span id="timer">59</span> сек
        </p>

        <!-- Кнопка повторной отправки (появляется после таймера) -->
        <button class="registration__btn registration__form" id="resend-btn" style="display: none;">Отправить код
            снова
        </button>
    </div>
</section>

<script>
    const myModal = new HystModal({linkAttributeName: 'data-hystmodal',});

    document.addEventListener("DOMContentLoaded", function () {
        let resendTimer;

        // Форма отправки номера телефона
        document.getElementById("phone-form").addEventListener("submit", function (event) {
            event.preventDefault();
            let phone = document.getElementById("phone-number").value;

            fetch("/ajax/auth/send_sms.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "phone=" + encodeURIComponent(phone)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("phone-number-send").value = phone;

                        document.getElementById("user-phone").textContent = phone;
                        document.getElementById("sms-message").style.display = "block";
                        document.getElementById("sms-message-desk").style.display = "block";
                        document.getElementById("code-form").style.display = "block";
                        document.getElementById("phone-form").style.display = "none";

                        startResendTimer();
                    } else {
                        document.querySelector('#alertModal .alertText .h2').textContent = "Ошибка отправки SMS. Попробуйте снова."
                        myModal.open('#alertModal');
                        //alert("Ошибка отправки SMS. Попробуйте снова.");
                    }
                });
        });

        // Форма проверки кода
        document.getElementById("code-form").addEventListener("submit", function (event) {
            event.preventDefault();
            let code = document.getElementById("sms-code").value;
            let phone = document.getElementById("phone-number-send").value;
            let utm_source = document.getElementById("UF_UTM_SOURCE").value;
            let utmCampaign = document.getElementById("UF_UTM_CAMPAIGN").value;
            let utmPartner = document.getElementById("UF_UTM_PARTNER").value;

            fetch("/ajax/auth/verify_sms.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "code=" + encodeURIComponent(code) +
                    "&phone=" + encodeURIComponent(phone) +
                    "&UF_UTM_SOURCE=" + encodeURIComponent(utm_source) +
                    "&UF_UTM_CAMPAIGN=" + encodeURIComponent(utmCampaign) +
                    "&UF_UTM_PARTNER=" + encodeURIComponent(utmPartner)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        //alert("Авторизация успешна!");
                        document.querySelector('#alertModal .alertText .h2').textContent = "Авторизация успешна!"
                        myModal.open('#alertModal');
                        window.location.href = "/profile/"; // Перенаправление в личный кабинет
                    } else {
                        //alert("Неверный код. Попробуйте снова.");
                        document.querySelector('#alertModal .alertText .h2').textContent = "Неверный код. Попробуйте снова."
                        myModal.open('#alertModal');
                    }
                });
        });

        // Запуск таймера повторной отправки
        function startResendTimer() {
            let timeLeft = 59;
            document.getElementById("resend-timer").style.display = "block";
            document.getElementById("resend-btn").style.display = "none";
            document.getElementById("timer").textContent = timeLeft;

            resendTimer = setInterval(function () {
                timeLeft--;
                document.getElementById("timer").textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(resendTimer);
                    document.getElementById("resend-timer").style.display = "none";
                    document.getElementById("resend-btn").style.display = "block";
                }
            }, 1000);
        }

        // Повторная отправка кода
        document.getElementById("resend-btn").addEventListener("click", function () {
            let phone = document.getElementById("user-phone").textContent;

            fetch("/ajax/auth/send_sms.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "phone=" + encodeURIComponent(phone)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        //alert("Код повторно отправлен!");
                        document.querySelector('#alertModal .alertText .h2').textContent = "Код повторно отправлен!"
                        myModal.open('#alertModal');
                        startResendTimer();
                    } else {
                        //alert("Ошибка повторной отправки.");
                        document.querySelector('#alertModal .alertText .h2').textContent = "Ошибка повторной отправки."
                        myModal.open('#alertModal');
                    }
                });
        });
    });
</script>
<div class="hystmodal" id="alertModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
             style="  min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2">Данные успешно отправлены. Спасибо за подписку!</p>
            </div>
        </div>
    </div>
</div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
