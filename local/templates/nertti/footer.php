<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
?>
</main>
<footer class="footer">
    <div class="footer__main">
        <nav class="footer__nav">
            <ul class="footer__list">
                <li class="footer__item">
                    <a href="#" class="footer__link">Каталог</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Повседневная одежда</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Мотоэкипировка</a>
                </li>
            </ul>
            <ul class="footer__list">
                <li class="footer__item">
                    <a href="#" class="footer__link">Клиентам</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Оплата и доставка</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Условия возврата</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Программа лояльности</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Новости</a>
                </li>
            </ul>
            <ul class="footer__list">
                <li class="footer__item">
                    <a href="#" class="footer__link">Контакты</a>
                </li>
                <li class="footer__item">
                    <a href="tel:+79996622835" class="footer__link">+7 999 662 28 35</a>
                </li>
                <li class="footer__item">
                    <a href="mailto:info@vl28.pro" class="footer__link">info@vl28.pro</a>
                    <span class="footer__subtext">для вопросов</span>
                </li>
                <li class="footer__item">
                    <a href="mailto:vl28@commerce.ru" class="footer__link">vl28@commerce.ru</a>
                    <span class="footer__subtext">для сотрудничества</span>
                </li>
            </ul>
            <ul class="footer__list">
                <li class="footer__item">
                    <a href="#" class="footer__link">Социальные сетиг</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Instagram</a>
                    <span class="footer__subtext">*компания meta признана в РФ экстремистской</span>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Telegram</a>
                </li>
            </ul>
            <ul class="footer__list">
                <li class="footer__item">
                    <a href="#" class="footer__link">Документация</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Договор оферты</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Политика конфиденциальности</a>
                </li>
                <li class="footer__item">
                    <a href="#" class="footer__link">Реквизиты </a>
                </li>
            </ul>
        </nav>
        <div class="subs">
            <p class="subs__text">Будьте в курсе всех новинок и специальных предложений</p>
            <a href="#" class="subs__btn main-btn">Подписаться</a>
        </div>
    </div>
    <div class="footer__bottom">
        <a href="#" class="footer__logo logo">
            <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/logo.svg" alt="VL28" class="logo__img">
        </a>
        <span>© VL28, 2024. Все права защищены</span>
    </div>
</footer>
<?php
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/jquery-3.7.1.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/hystmodal.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/swiper-bundle.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/mask.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/assets/js/main.min.js');
?>
<button class="hystmodal__shadow"></button>
</body>
</html>