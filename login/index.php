<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход или регистрация");
?>
    <section class="registration">
        <div class="container">
            <p class="h2">Вход или регистрация</p>
            <form action="#" class="registration__form">
                <input type="text" class="form-input phone-input" placeholder="+7">
                <input type="submit" class="registration__btn" value="Далее">
            </form>
        </div>
    </section>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>