<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$email = $request->get('email');
$checkWord = $request->get('check-word');
?>
<section class="registration">
    <div class="container">
        <p class="h2">Восстановление пароля</p>
        <?php if (empty($email) || empty($checkWord)): ?>
            <form action="/ajax/auth/password-recovery.php" class="registration__form" id="recovery_form_sent">
                <div id="sms-message-desk" class="registration__desc"><p class="gray">Введите адрес электронной почты и
                        мы
                        отправим на него письмо для восстановления пароля </p></div>
                <input type="text" class="form-input" name="email" id="email" placeholder="E-mail"
                       style="margin-top: 10px">
                <button type="submit" class="registration__btn">Отправить</button>
            </form>
        <?php else: ?>
            <form action="/ajax/auth/password-recovery.php" class="registration__form" id="recovery_form_get">
                <div id="sms-message-desk" class="registration__desc">
                    <p class="gray">Введите новый пароль</p>
                </div>

                <input type="hidden" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" name="email">
                <input type="hidden" value="<?= htmlspecialchars($checkWord, ENT_QUOTES, 'UTF-8') ?>" name="checkWord">

                <input type="password" class="form-input" name="password" id="password" placeholder="Пароль"
                       style="margin-top: 10px">
                <input type="password" class="form-input" name="confirmPassword" id="confirm_password"
                       placeholder="Подтвердите пароль" style="margin-top: 10px">

                <button type="submit" class="registration__btn">Отправить</button>
            </form>
        <?php endif; ?>
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
        let authModal = new HystModal({
            linkAttributeName: 'data-hystmodal',
            afterClose: function (modal) {
                window.location.href = '/';
            }
        });
        let errorModal = new HystModal({
            linkAttributeName: 'data-hystmodal',
        });
        let recovery_form_sent = document.getElementById("recovery_form_sent");
        let recovery_form_get = document.getElementById("recovery_form_get");

        if (recovery_form_sent) {
            recovery_form_sent.addEventListener("submit", function (event) {
                event.preventDefault();
                const formData = new FormData(recovery_form_sent);

                fetch("/ajax/auth/password-recovery.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {
                            let errorMessages = [];

                            if (data.message.EMAIL) {
                                errorMessages.push(data.message.EMAIL);
                            }
                            if (errorMessages.length > 0) {
                                document.querySelector('#authModal .alertText .gray').innerHTML = errorMessages.join('<br>');
                                errorModal.open('#authModal');
                            }
                        } else {
                            document.querySelector('#authModal .alertText .h2').innerHTML = "Успешная отправка";
                            document.querySelector('#authModal .alertText .gray').innerHTML = "Сообщение с ссылкой на восстановление успешно отправлено на указанный электронный адрес";
                            authModal.open('#authModal');
                        }
                    });
            });
        }
        if (recovery_form_get) {
            recovery_form_get.addEventListener("submit", function (event) {
                event.preventDefault();
                const formData = new FormData(recovery_form_get);

                fetch("/ajax/auth/password-recovery-email.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {
                            let errorMessages = [];

                            if (data.message.CHECK_WORD) {
                                errorMessages.push(data.message.CHECK_WORD);
                            }
                            if (data.message.PASSWORD) {
                                errorMessages.push(data.message.PASSWORD);
                            }
                            if (data.message.CONFIRM_PASSWORD) {
                                errorMessages.push(data.message.CONFIRM_PASSWORD);
                            }
                            if (errorMessages.length > 0) {
                                document.querySelector('#authModal .alertText .gray').innerHTML = errorMessages.join('<br>');
                                errorModal.open('#authModal');
                            }
                        } else {
                            document.querySelector('#authModal .alertText .h2').innerHTML = "Пароль изменён";
                            document.querySelector('#authModal .alertText .gray').innerHTML = "Вы успешно изменили пароль, войдите с новыми данными";
                            authModal.open('#authModal');
                        }
                    });
            });
        }

    });
</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
