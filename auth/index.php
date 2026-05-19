<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Вход");
?>
<section class="registration">
    <div class="container">
        <p class="h2">Вход</p>

        <form action="#" class="registration__form" id="phone-form">
            <input type="text" class="form-input phone-input" id="phone-number" placeholder="+7">
            <input type="submit" class="registration__btn" value="Далее">
        </form>
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

        <!-- Форма ввода данных (скрыта до подтверждения SMS) -->
        <form action="#" class="registration__form" id="auth_form" style="display: none;">
            <input type="hidden" name="id" id="id-user" placeholder="">
            <input type="text" class="form-input" name="email" id="email" placeholder="E-mail">
            <input type="password" class="form-input" name="password" id="password" placeholder="Пароль" style="margin-top: 10px">
            <input type="password" class="form-input" name="confirm_password" id="confirm_password" placeholder="Подтвердите пароль" style="margin-top: 10px">
            <input type="submit" class="registration__btn" value="Подтвердить">
        </form>
    </div>
</section>

<script>

    document.addEventListener("DOMContentLoaded", function () {
        const myModal = new HystModal({linkAttributeName: 'data-hystmodal',});

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
                    }
                });
        });

        // Форма проверки кода
        document.getElementById("code-form").addEventListener("submit", function (event) {
            event.preventDefault();
            let code = document.getElementById("sms-code").value;
            let phone = document.getElementById("phone-number-send").value;

            fetch("/ajax/auth/verify_sms.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "code=" + encodeURIComponent(code) +
                    "&phone=" + encodeURIComponent(phone)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("auth_form").style.display = "block";
                        document.getElementById("sms-message").remove();
                        document.getElementById("sms-message-desk").remove();
                        document.getElementById("code-form").remove();
                        document.getElementById("resend-timer").remove();
                        document.getElementById("resend-btn").remove();

                        document.getElementById("id-user").value = data.id;
                    } else {
                        document.querySelector('#alertModal .alertText .h2').textContent = data.error;
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
                        document.querySelector('#alertModal .alertText .h2').textContent = "Код повторно отправлен!"
                        myModal.open('#alertModal');
                        startResendTimer();
                    } else {
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
             style="min-height: auto;">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="alertText">
                <p class="h2"></p>
            </div>
        </div>
    </div>
</div>

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

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            const formData = new FormData(form);

            fetch("/ajax/auth/auth.php", {
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
