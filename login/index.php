<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход или регистрация");
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
            <p class="registration__desc" id="sms-message" style="display: none;">
                Введите код, который мы выслали на номер <span id="user-phone"></span>
            </p>

            <!-- Форма ввода кода (скрыта до отправки SMS) -->
            <form action="#" class="registration__form" id="code-form" style="display: none;">
                <input type="text" class="registration__code" id="sms-code" placeholder="Код из СМС">
                <input type="submit" class="registration__btn" value="Подтвердить">
            </form>

            <!-- Таймер повторной отправки -->
            <p class="registration__desc" id="resend-timer" style="display: none;">
                Повторная отправка через <span id="timer">59</span> сек
            </p>

            <!-- Кнопка повторной отправки (появляется после таймера) -->
            <button class="registration__btn" id="resend-btn" style="display: none;">Отправить код снова</button>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let resendTimer;

            // Форма отправки номера телефона
            document.getElementById("phone-form").addEventListener("submit", function (event) {
                event.preventDefault();
                let phone = document.getElementById("phone-number").value;

                fetch("/ajax/send_sms.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "phone=" + encodeURIComponent(phone)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById("user-phone").textContent = phone;
                            document.getElementById("sms-message").style.display = "block";
                            document.getElementById("code-form").style.display = "block";
                            document.getElementById("phone-form").style.display = "none";

                            startResendTimer();
                        } else {
                            alert("Ошибка отправки SMS. Попробуйте снова.");
                        }
                    });
            });

            // Форма проверки кода
            document.getElementById("code-form").addEventListener("submit", function (event) {
                event.preventDefault();
                let code = document.getElementById("sms-code").value;

                fetch("/ajax/verify_sms.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "code=" + encodeURIComponent(code)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Авторизация успешна!");
                            window.location.href = "/profile/"; // Перенаправление в личный кабинет
                        } else {
                            alert("Неверный код. Попробуйте снова.");
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

                fetch("/ajax/send_sms.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "phone=" + encodeURIComponent(phone)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Код повторно отправлен!");
                            startResendTimer();
                        } else {
                            alert("Ошибка повторной отправки.");
                        }
                    });
            });
        });
    </script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>