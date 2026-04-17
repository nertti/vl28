<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("PRINT_TITLE", "N");
$APPLICATION->SetTitle("Ты почти в клубе VL28");
?><style>
    /* ===== ОСНОВА ===== */

    .promo {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 20px;
        margin-top: -50px;
    }

    .promo__inner {
        width: 100%;
        max-width: 680px;
    }

    .promo__content {
        text-align: center;
    }

    /* ===== ТЕКСТ ===== */

    .promo__subtitle {
        font-size: 18px;
        margin-bottom: 8px;
    }

    .promo__text {
        color: #959595;
        margin-bottom: 40px;
    }

    .promo__step {
        font-size: 13px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #959595;
        margin-bottom: 10px;
    }

    .promo__title {
        font-size: 30px;
        line-height: 1.3;
        margin-bottom: 10px;
    }

    /* ===== СКИДКА (главный акцент) ===== */

    .promo__discount {
        font-size: 72px;
        font-weight: 700;
        margin: 10px 0 20px;
        position: relative;
        display: inline-block;
    }

    /* лёгкий glow эффект */
    .promo__discount::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle, rgba(0,0,0,0.08) 0%, transparent 70%);
        z-index: -1;
    }

    /* ===== ДЕЙСТВИЕ ===== */

    .promo__site {
        color: #959595;
        margin-bottom: 40px;
    }

    .promo__btn {
        padding: 18px 36px;
        background: #000;
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .promo__btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }

    .promo__note {
        font-size: 12px;
        color: #959595;
        margin-top: 25px;
    }

    /* ===== НИЖНИЕ ССЫЛКИ ===== */

    .promo__bottom {
        margin-top: 50px;
    }

    .promo__alt {
        margin-bottom: 10px;
    }

    .promo__links {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
    }

    .promo__link {
        color: #000;
        text-decoration: none;
        position: relative;
    }

    .promo__link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 1px;
        background: #000;
        transition: 0.3s;
    }

    .promo__link:hover::after {
        width: 100%;
    }

    .promo__divider {
        width: 20px;
        height: 1px;
        background: #ccc;
    }

    /* ===== АНИМАЦИИ ===== */

    .fade-up {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeUp 0.8s ease forwards;
    }

    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }

    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== АДАПТИВ ===== */

    @media (max-width: 768px) {

        .promo {
            min-height: auto;
            padding: 60px 20px;
        }

        .promo__title {
            font-size: 24px;
        }

        .promo__discount {
            font-size: 56px;
        }

        .promo__btn {
            width: 100%;
            padding: 16px;
        }

        .promo__links {
            flex-direction: column;
            gap: 10px;
        }

        .promo__divider {
            display: none;
        }
    }

    @media (max-width: 480px) {

        .promo__discount {
            font-size: 44px;
        }

        .promo__subtitle {
            font-size: 16px;
        }

    }
</style>
<div class="promo container">
	<div class="promo__inner">
		<div class="promo__content">
			<div class="promo__top fade-up">
				<p class="promo__subtitle">
					Круто. Ты почти в клубе VL28
				</p>
				<p class="promo__text">
					Закрытый доступ. Эксклюзивные условия. Только для своих
				</p>
			</div>
			<div class="promo__center fade-up delay-1">
				<p class="promo__step">
					Остался один шаг
				</p>
				<h1 class="promo__title">
				Зарегистрируйся и получи </h1>
				<div class="promo__discount">
					–35%
				</div>
				<p class="promo__site">
					на первый заказ на vl28.pro
				</p>
			</div>
			<div class="promo__action fade-up delay-2">
 <a href="/login/" class="promo__btn">ВСТУПИТЬ В КЛУБ</a>
				<p class="promo__note">
					Только номер телефона. Без спама.
				</p>
			</div>
			<div class="promo__bottom fade-up delay-3">
				<p class="promo__alt">
					Или уже сейчас:
				</p>
				<div class="promo__links">
 <a href="/catalog/" class="promo__link">Смотреть каталог</a> <span class="promo__divider"></span> <a href="/about/" class="promo__link">Узнать о бренде</a>
				</div>
			</div>
		</div>
	</div>
</div><?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>