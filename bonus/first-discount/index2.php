<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Скидка 35% на первый заказ");
?>
    <style>
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
    </style>
    <section class="promo-sale">

        <!-- HERO -->
        <div class="promo__hero">
            <div class="promo__hero-images">
                <img src="https://vl28.pro/upload/medialibrary/7bb/ziv6pxnogyjhx9z5xg9isvj6fl5qv6q6.png" alt="">
                <img src="https://vl28.pro/upload/medialibrary/2e2/lgl0td1o0v2ufnll0l5oaq3mg3svrv1r.png" alt="">
                <img src="https://vl28.pro/upload/medialibrary/959/9w3z2g7o2qwihfle8me5xe7ti4n3d1c6.png" alt="">
                <img src="https://vl28.pro/upload/medialibrary/2ae/ay6mmiio56chkol4a0g3g05pwrmx09ek.png" alt="">
                <img src="https://vl28.pro/upload/medialibrary/95d/nfc9mff7hywxvz1kr2snsihzfrfqen6b.png" alt="">
            </div>

            <div class="promo__hero-content">
                <div class="promo__left">
                    <p class="promo__subtitle">Ты почти<br>в клубе VL28</p>
                </div>

                <div class="promo__center">
                    <div class="promo__discount">-35%</div>
                    <p class="promo__text">
                        Забирай промокод на скидку<br>
                        на первый заказ после регистрации:
                    </p>
                    <p class="promocode">EMPOWERED35</p>
                    <p class="promo__text top40">
                        Регистрируйся, используй промокод и забирай<br>
                        заряженные вещи по заряженной скидке:
                    </p>
                    <button onclick="location.href='/login/'" class="main-btn">Вступить в клуб</button>
                </div>

                <div class="promo__right">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="94" height="395" viewBox="0 0 94 395" fill="none">
<path d="M29.3441 61.9823C13.1265 61.9823 -1.98609e-06 75.1087 -1.27719e-06 91.3264C-5.68294e-07 107.544 13.1309 120.545 29.3442 120.545C43.2053 120.545 54.9699 110.797 57.8729 97.706L78.051 117.888L91.3701 117.888L91.3701 61.9823L77.7777 61.9823L77.7777 98.3242L50.0598 70.6063C44.7823 65.3288 37.4485 61.9823 29.3352 61.9823L29.3441 61.9823ZM44.9705 91.3264C44.9705 99.946 37.9503 107.083 29.3441 107.083C20.738 107.083 13.5879 99.9415 13.5879 91.3264C13.5879 82.7113 20.5991 75.7001 29.3441 75.7001C38.0892 75.7001 44.9705 82.7158 44.9705 91.3264Z" fill="#555"/>
<path d="M46.5743 8.75894C41.8165 3.44116 34.9935 0.000502267 27.0638 0.000502614C12.114 0.000503267 -1.83648e-06 12.1145 -1.183e-06 27.0643C-5.2952e-07 42.0141 12.114 54.1281 27.0638 54.1281C34.9935 54.1281 41.8165 50.6875 46.5743 45.3697C51.3321 50.6875 58.1552 54.1281 66.0848 54.1281C81.1646 54.1281 93.1486 42.0097 93.1486 27.0643C93.1486 12.1189 81.1601 0.000500249 66.0848 0.000500908C58.1552 0.000501255 51.3321 3.44116 46.5743 8.75894ZM79.5607 27.0688C79.5607 34.416 73.4321 40.5447 66.0848 40.5447C58.7376 40.5447 52.6089 34.416 52.6089 27.0688C52.6089 19.7216 58.7376 13.5929 66.0848 13.5929C73.4321 13.5929 79.5607 19.7216 79.5607 27.0688ZM40.5397 26.9434C40.5397 34.4205 34.4066 40.5447 27.1937 40.5447C19.9809 40.5447 13.5924 34.416 13.5924 26.9434C13.5924 19.4707 19.7166 13.5974 27.1937 13.5974C34.6709 13.5974 40.5397 19.726 40.5397 26.9434Z" fill="#555"/>
<path d="M1.77392 142.242L78.1225 186.312L91.3744 186.312L91.3744 126.606L77.7865 126.606L77.7865 170.403L1.77392 126.53L1.77392 142.242Z" fill="#555"/>
<path d="M1.77392 169.901L78.3106 214.087L1.77393 214.087L1.77393 227.679L91.3744 227.679L91.3744 221.631L91.3789 221.631L91.3789 205.938L1.77392 154.207L1.77392 169.901Z" fill="#555"/>
<path d="M1.77393 309.702L1.77393 323.295L91.3789 323.295L91.3789 309.702L1.77393 309.702Z" fill="#555"/>
<path d="M1.77393 357.508L1.77393 371.1L91.3789 371.1L91.3789 357.508L1.77393 357.508Z" fill="#555"/>
<path d="M1.77393 381.408L1.77393 395.001L91.3789 395.001L91.3789 381.408L1.77393 381.408Z" fill="#555"/>
</svg>
                    </span>
                </div>
            </div>
        </div>


    </section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>