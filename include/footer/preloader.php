<div id="site-preloader">
    <div class="preloader-content">
        <img src="/local/templates/public/assets/img/logo.svg" alt="Логотип" class="preloader-logo">
        <div class="loader-dots">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>

<script>
    // Слушаем полную загрузку страницы напрямую (без DOMContentLoaded)
    window.addEventListener('load', function () {
        const preloader = document.getElementById('site-preloader');
        if (preloader) {
            preloader.classList.add('hide'); // Запускает плавное скрытие за 0.6s
            document.body.classList.remove('preloader-active'); // Возвращает прокрутку сайту
            
            // Удаляем элемент из дерева DOM через 600мс (время вашей transition анимации)
            setTimeout(() => preloader.remove(), 600);
        }
    });
</script>
