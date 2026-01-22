<div class="cookies">
    <button class="hystmodal__close" id="cookies-close"></button>
    <div class="cookies_body">
        Мы используем файлы cookie. Оставаясь на сайте, вы соглашаетесь с
        <a href="https://vl28.pro/personal/" class="link">использованием файлов cookie</a>.
        Они помогают нам делать сайт лучше для вас.
    </div>
    <div class="cookies_button">Принять</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cookieBox = document.querySelector('.cookies');
        const closeBtn = document.querySelector('#cookies-close');
        const acceptBtn = document.querySelector('.cookies_button');

        const STORAGE_KEY = 'cookies_hidden_until';

        const now = Date.now();
        const hiddenUntil = localStorage.getItem(STORAGE_KEY);

        // Если срок скрытия ещё не вышел — не показываем
        if (hiddenUntil && now < hiddenUntil) {
            cookieBox.style.display = 'none';
            return;
        }

        // Плавное скрытие
        function hideCookies(durationMs) {
            cookieBox.classList.add('cookies--hide');

            // Ждём окончания анимации
            setTimeout(() => {
                cookieBox.style.display = 'none';
                localStorage.setItem(STORAGE_KEY, Date.now() + durationMs);
            }, 300);
        }

        // Закрыть на 5 минут
        closeBtn.addEventListener('click', function () {
            hideCookies(5 * 60 * 1000);
        });

        // Принять на 1 год
        acceptBtn.addEventListener('click', function () {
            hideCookies(365 * 24 * 60 * 60 * 1000);
        });
    });
</script>