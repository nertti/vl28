<div id="site-preloader">
    <div class="preloader-content">
        <img src="/local/templates/public/assets/img/logo.svg" alt="Логотип" class="preloader-logo">
        <div class="loader-dots">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('load', function () {
            const preloader = document.getElementById('site-preloader');
            if (preloader) {
                preloader.classList.add('hide');
                setTimeout(() => preloader.remove(), 700);
            }
        });
    });
</script>
