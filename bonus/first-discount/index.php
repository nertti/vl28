<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Скидка 35% на первый заказ");
?><style>
        body{
            line-height: unset !important;
        }
        .promo-sale {
            font-family: Arial, sans-serif;
            margin: -100px 0 0 !important;
            padding: 0 !important;
            overflow: hidden;
        }

        /* HERO */

        .promo__hero {
            background: #000;
            color: #fff;
        }

        .promo__hero-images {
            display: flex;
            /*height: 480px;*/
        }

        .promo__hero-images img {

        }

        /* контент поверх */

        .promo__hero-content {
            display: flex;
            justify-content: space-between;
            padding: 40px;
            margin: -250px 0 0;
            z-index: 100;
            position: relative;
            background: #000;
        }

        /* левая часть */

        .promo__subtitle {
            color: #c6ff00;
            font-size: 38px;
            font-weight: lighter;
        }

        /* центр */

        .promo__discount {
            font-size: 144px;
            color: #c6ff00;
            font-weight: normal;
        }

        .promo__text {
            margin: 10px 0 20px;
            color: #aaa;
        }

        .promocode{
            text-decoration: underline;
            letter-spacing: 30%;
            margin-bottom: 20px;
        }

        .promo__btn {
            background: #c6ff00;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
        }

        /* правая часть */

        .promo__right span {
            writing-mode: vertical-rl;
            opacity: 0.2;
            font-size: 40px;
        }
        @media (max-width: 1000px) {
            .promo__hero-images img:last-child{
                display: none;
            }
        }
        @media (max-width: 750px) {
            .promo__right{
                display: none;
            }
        }
        @media (max-width: 640px) {
            .promo__hero-images {
                height: 480px;
            }
            .promo__subtitle {
                font-size: 18px;
            }
            .promo__discount {
                font-size: 80px;
            }
        }
    </style> <section class="promo-sale">
<!-- HERO -->
<div class="promo__hero">
	<div class="promo__hero-images">
 <img src="https://vl28.pro/upload/medialibrary/7bb/ziv6pxnogyjhx9z5xg9isvj6fl5qv6q6.png" alt=""> <img src="https://vl28.pro/upload/medialibrary/2e2/lgl0td1o0v2ufnll0l5oaq3mg3svrv1r.png" alt=""> <img src="https://vl28.pro/upload/medialibrary/959/9w3z2g7o2qwihfle8me5xe7ti4n3d1c6.png" alt=""> <img src="https://vl28.pro/upload/medialibrary/2ae/ay6mmiio56chkol4a0g3g05pwrmx09ek.png" alt=""> <img src="https://vl28.pro/upload/medialibrary/95d/nfc9mff7hywxvz1kr2snsihzfrfqen6b.png" alt="">
	</div>
	<div class="promo__hero-content">
		<div class="promo__left">
			<p class="promo__subtitle">
				 Ты почти<br>
				 в клубе VL28
			</p>
		</div>
		<div class="promo__center">
			<div class="promo__discount">
				 -35%
			</div>
			<p class="promo__text">
				 Зарегистрируйся и получи скидку на первый заказ по промокоду:
			</p>
			<p class="promocode">Empowered35</p>
			<p class="promo__text top40">
				 Закрытый доступ. Эксклюзивные условия. Только для своих.
			</p>
 <button onclick="location.href='/login/'" class="main-btn">Вступить в клуб</button>
		</div>
		<div class="promo__right">
		</div>
	</div>
</div>
 </section> <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>