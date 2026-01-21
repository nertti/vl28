<div class="cookies">
    <button class="hystmodal__close" id="cookies-close"></button>
    <div class="cookies_body">
        Мы используем файлы cookie. Оставаясь на сайте, вы соглашаетесь с
        <a href="https://vl28.pro/personal/" class="link">использованием файлов cookie</a>.
        Они помогают нам делать сайт лучше для вас.
    </div>
    <div class="cookies_button">Принять</div>
</div>
<style>
    .cookies {
        z-index: 99999999;
        position: fixed;
        right: 0;
        bottom: 0;
        width: 100%;
        max-width: 425px;
        padding: 40px;
        margin: 35px;
        background-color: white;
        border: 1px solid black;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .cookies--hide {
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
    }

    .cookies .cookies_button {
        padding: 15px;
        width: 100%;
        border: 1px solid black;
        text-align: center;
        margin-top: 30px;
        cursor: pointer;
    }

    .cookies .cookies_body {
        text-align: justify;
        font-size: 14px !important;
    }

    .cookies .link {
        padding-bottom: 0;
    }

    .cookies .hystmodal__close {
        top: 15px !important;
        right: 15px !important;
    }

    @media screen and (max-width: 500px) {
        .cookies {
            bottom: 0;
            max-width: 100%;
            margin: 0;
        }
        .cookies .cookies_body {
            font-size: 12px !important;
        }
        .cookies .link {
            font-size: 12px !important;
        }
    }
</style>
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