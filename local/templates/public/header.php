<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
require($_SERVER['DOCUMENT_ROOT'] . '/include/header/remember_auth.php');
require($_SERVER['DOCUMENT_ROOT'] . '/include/header/check_referral_link.php');
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Loader;
Loader::includeModule('sale');
use Bitrix\Sale\DiscountCouponsManager;

/** @var \CMain $APPLICATION */
/** @var \CMain $USER */

$isMainPage = $APPLICATION->GetCurPage(false) === '/';
$isServicePages = strpos($APPLICATION->GetCurPage(false), '/ajax/') !== false;
$isAboutPage = $APPLICATION->GetCurPage(false) === '/about/';
$isCartPage = $APPLICATION->GetCurPage(false) === '/cart/';
$isOrderPage = $APPLICATION->GetCurPage(false) === '/cart/order/';
$isProfilePages = strpos($APPLICATION->GetCurPage(false), '/profile/') !== false;
$isFavoritePage = $APPLICATION->GetCurPage(false) === '/profile/favorite/';
$isCustomersPage = $APPLICATION->GetCurPage(false) === '/customers/';

function checkCatalogPath($url) {
    // Проверяем начало пути
    if (strpos($url, '/catalog/') !== 0) {
        return false;
    }

    // Разбиваем путь и считаем уровни
    $parts = explode('/', $url);
    $depth = count(array_filter($parts));
    // Возвращаем true только если глубина равна 3
    return $depth >= 3;
}

if ($isProfilePages && !$isFavoritePage){
    if (!$USER->IsAuthorized()) {
        header('Location: /login/');
        exit();
    }
}

if (!$isCartPage && !$isCartPage) {
    DiscountCouponsManager::clear(true);
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta property="og:image" content="https://vl28.pro/local/templates/public/assets/img/vl28.jpg">
    <link rel="preload" as="image" href="/local/templates/public/assets/img/logo.svg">
    <?php Asset::getInstance()->addString("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>") ?>
    <title><?php $APPLICATION->ShowTitle(); ?> - vl28.pro</title>
    <?php if($isOrderPage):?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@cdek-it/widget@3" charset="utf-8"></script>
    <?php endif;?>
    <?php
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/hystmodal.min.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/swiper-bundle.min.css');
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/css/main.min.css');
    ?>
    <?php $APPLICATION->ShowHead(); ?>
</head>
<body>
<?php $APPLICATION->ShowPanel(); ?>

<?php if (checkCatalogPath($APPLICATION->GetCurPageParam())):?>
<header class="header header_transparent">
    <div class="header__left">
      <a href="#" data-hystmodal="#menuModal" class="menu-btn">
        <svg width="21" height="10" viewBox="0 0 21 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="menu-btn__icon svg replaced-svg">
<path d="M1 1.69629H20" stroke="#929292" stroke-width="1.5" stroke-linecap="round"></path>
<path d="M1 8.69629H20" stroke="#929292" stroke-width="1.5" stroke-linecap="round"></path>
</svg>
        <span class="menu-btn__text">Меню</span>
      </a>
      <a href="#" data-hystmodal="#searchModal" class="search-btn">
          <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/search.svg" alt="menu icon" class="search-btn__icon svg">
          <span class="search-btn__text">Поиск</span>
        </a>
    </div>
    <a href="/" class="header__logo logo">
      <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
      <a href="/profile/favorite/" class="header__link">
        <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon svg replaced-svg">
<g clip-path="url(#clip0_291_84)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.76985 0.742123C4.74967 0.753839 4.65611 0.781455 4.56193 0.803518C2.66207 1.24836 1.32015 2.29401 0.538952 3.93829C0.484723 4.05245 0.440352 4.15697 0.440352 4.17059C0.440352 4.18422 0.424331 4.23375 0.404713 4.28064C0.385121 4.32756 0.369368 4.36848 0.369711 4.37159C0.370053 4.37472 0.34322 4.45431 0.310052 4.54848C0.199784 4.86177 0.115493 5.24607 0.0339177 5.80739C-0.00920602 6.10404 -0.000742707 7.29488 0.0464415 7.56935C0.364378 9.41904 1.09873 10.6924 2.92201 12.5557C3.77463 13.427 5.11317 14.6134 6.45938 15.691C7.11174 16.2133 7.51749 16.5228 8.53675 17.2758C8.73775 17.4242 9.10431 17.5962 9.33766 17.6516C9.40165 17.6668 9.60665 17.6836 9.79321 17.6889C10.1669 17.6996 10.304 17.6765 10.6452 17.5455C10.9356 17.4341 11.4565 17.0678 12.6461 16.1389C14.1505 14.9641 15.5243 13.7682 16.5739 12.7196C18.2171 11.078 19.0071 9.91581 19.385 8.58445C19.4079 8.50374 19.4361 8.41017 19.4475 8.37654C19.4872 8.26021 19.5924 7.70021 19.6222 7.44629C19.6428 7.27032 19.6573 7.21012 19.669 7.25192C19.6782 7.28524 19.6858 6.99881 19.6858 6.61539C19.6858 6.23175 19.677 5.94576 19.6663 5.97942C19.6477 6.03732 19.6465 6.03631 19.6442 5.9609C19.6346 5.64815 19.4834 4.94095 19.3209 4.44922C19.1451 3.91718 18.7767 3.21806 18.4786 2.85054C18.304 2.63519 17.9831 2.29746 17.7885 2.12423C17.5706 1.93021 17.1086 1.58711 17.0264 1.5582C17.0052 1.55074 16.9713 1.53065 16.9511 1.51353C16.8455 1.42393 16.1889 1.1286 15.8504 1.01843C15.1202 0.780746 14.6452 0.710177 13.8814 0.725905C13.4479 0.734809 13.3517 0.744691 13.0375 0.812618C11.943 1.04915 11.0668 1.52231 10.2812 2.30108C10.0485 2.53177 9.95825 2.60574 9.89475 2.61765C9.76249 2.64245 9.6509 2.59025 9.53511 2.44941C9.18858 2.02791 8.50535 1.51791 7.90078 1.22952C7.6056 1.08873 6.95945 0.879713 6.62884 0.818072C6.47413 0.789233 6.30408 0.756407 6.25093 0.745131C6.1218 0.717711 4.81649 0.715069 4.76985 0.742123ZM5.11229 2.39907C5.07866 2.40533 4.95208 2.42679 4.831 2.44672C4.40979 2.51607 3.79914 2.76664 3.39236 3.03705C2.54463 3.60062 1.98028 4.45561 1.76503 5.50244C1.68304 5.90115 1.65785 6.16253 1.65772 6.61539C1.6575 7.48009 1.82719 8.21711 2.20121 8.97582C2.38855 9.35586 2.81575 9.99068 3.07467 10.2738C3.11239 10.3151 3.20763 10.4251 3.28635 10.5184C3.86677 11.2063 4.85438 12.1739 5.91871 13.0974C6.00398 13.1714 6.08582 13.2433 6.10057 13.2572C6.11534 13.271 6.23747 13.3743 6.37201 13.4867C6.68363 13.747 6.80305 13.8476 6.84899 13.8887C6.93149 13.9625 7.93909 14.7695 8.29215 15.0445C8.79807 15.4387 9.507 15.959 9.61206 16.0133C9.72866 16.0736 9.94215 16.0712 10.0596 16.0082C10.1791 15.9442 10.8812 15.4278 11.4353 14.9964C13.8508 13.116 15.6661 11.4376 16.7432 10.0888C17.4568 9.19508 17.8453 8.30463 17.9811 7.25136C18.0192 6.95576 18.0187 6.26044 17.9801 5.94273C17.8706 5.04092 17.5558 4.30256 17.0145 3.67801C16.6001 3.19988 15.9748 2.79276 15.3123 2.56983C14.947 2.44689 14.6772 2.40355 14.1932 2.39C13.4968 2.3705 13.0326 2.46147 12.4839 2.72489C12.066 2.92551 11.7799 3.12905 11.4109 3.4881C11.236 3.65827 11.0536 3.83099 11.0056 3.87193C10.8513 4.00368 10.5657 4.13955 10.2991 4.20817C10.0693 4.26731 10.0151 4.27252 9.761 4.25995C9.56338 4.25014 9.41897 4.22781 9.28045 4.18559C8.90136 4.07009 8.75626 3.97354 8.31661 3.54429C7.91551 3.15267 7.67643 2.97348 7.297 2.78007C6.7227 2.48733 6.38544 2.40805 5.66265 2.39589C5.39359 2.39137 5.14593 2.39281 5.11229 2.39907ZM0.0108515 6.63985C0.0108515 7.02327 0.0142271 7.18011 0.0183609 6.98841C0.0224947 6.79669 0.0224947 6.48299 0.0183609 6.29129C0.0142271 6.09957 0.0108515 6.25643 0.0108515 6.63985Z" fill="#929292"></path>
</g>
<defs>
<clipPath id="clip0_291_84">
<rect width="19.6662" height="17" fill="white" transform="translate(0 0.696289)"></rect>
</clipPath>
</defs>
</svg>
      </a>
      <a href="/profile/" class="header__link">
        <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon svg replaced-svg">
<path d="M7.50007 10.1963C6.23444 10.1963 5.02 9.69589 4.12503 8.80528C3.23005 7.91467 2.72732 6.70595 2.72732 5.44631C2.72732 4.18667 3.23016 2.97806 4.12503 2.08734C5.01989 1.19663 6.2344 0.696289 7.50007 0.696289C8.76574 0.696289 9.98013 1.19674 10.8751 2.08734C11.7701 2.97795 12.2728 4.18667 12.2728 5.44631C12.2711 6.70591 11.7683 7.91283 10.8734 8.80354C9.97849 9.69417 8.76569 10.1946 7.50007 10.1963ZM7.50007 2.05351C6.59582 2.05351 5.72905 2.4106 5.08982 3.04761C4.44977 3.68377 4.09097 4.54639 4.09097 5.44638C4.09097 6.34636 4.44977 7.20896 5.08982 7.84514C5.72903 8.48215 6.59577 8.83924 7.50007 8.83924C8.40436 8.83924 9.27109 8.48215 9.91031 7.84514C10.5504 7.20898 10.9092 6.34636 10.9092 5.44638C10.9083 4.54728 10.5487 3.68464 9.90946 3.04848C9.27026 2.41232 8.40349 2.05436 7.50007 2.05351ZM7.50007 19.6963C5.39837 19.6954 3.30008 19.508 1.23238 19.1348C0.516488 19.0059 -0.002565 18.3841 9.53502e-06 17.6606C0.00171396 15.8615 0.721032 14.137 1.99944 12.8648C3.27785 11.5925 5.01056 10.8766 6.81819 10.8749H8.18182C9.98949 10.8766 11.7222 11.5925 13.0006 12.8648C14.279 14.1371 14.9983 15.8616 15 17.6606C15.0017 18.3816 14.4852 19.0016 13.7727 19.1322C11.7026 19.5063 9.60355 19.6954 7.50007 19.6963ZM6.81825 12.232C5.37195 12.2337 3.98539 12.8063 2.96343 13.8241C1.94071 14.8411 1.36543 16.2212 1.36373 17.6606C1.36543 17.7259 1.41146 17.7819 1.47538 17.7963C5.46154 18.5122 9.54371 18.5122 13.529 17.7963C13.5929 17.7827 13.6381 17.7259 13.6364 17.6606C13.6347 16.2212 13.0594 14.8412 12.0367 13.8241C11.0148 12.8063 9.62818 12.2337 8.18186 12.232H6.81825Z" fill="#929292"></path>
</svg>
      </a>
            <a href="#" data-hystmodal="#cartModal" class="header__link header__cart">
        <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/cart.svg" alt="cart icon" class="header__link-icon header__link-icon_cart svg">
        <?php $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket", array(
                    "HIDE_ON_BASKET_PAGES" => "N",	// Не показывать на страницах корзины и оформления заказа
                        "PATH_TO_BASKET" => "/cart/",	// Страница корзины
                        "PATH_TO_ORDER" => "/cart/order/",	// Страница оформления заказа
                        "PATH_TO_PERSONAL" => SITE_DIR."personal/",	// Страница персонального раздела
                        "PATH_TO_PROFILE" => SITE_DIR."personal/",	// Страница профиля
                        "PATH_TO_REGISTER" => SITE_DIR."login/",	// Страница регистрации
                        "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
                        "POSITION_HORIZONTAL" => "right",
                        "POSITION_VERTICAL" => "top",
                        "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
                        "SHOW_DELAY" => "N",
                        "SHOW_EMPTY_VALUES" => "N",	// Выводить нулевые значения в пустой корзине
                        "SHOW_IMAGE" => "Y",
                        "SHOW_NOTAVAIL" => "N",
                        "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
                        "SHOW_PERSONAL_LINK" => "N",	// Отображать персональный раздел
                        "SHOW_PRICE" => "Y",
                        "SHOW_PRODUCTS" => "N",	// Показывать список товаров
                        "SHOW_SUMMARY" => "Y",
                        "SHOW_TOTAL_PRICE" => "N",	// Показывать общую сумму по товарам
                        "COMPONENT_TEMPLATE" => "bootstrap_v4",
                        "PATH_TO_AUTHORIZE" => "",	// Страница авторизации
                        "SHOW_REGISTRATION" => "N",	// Добавить возможность регистрации
                        "MAX_IMAGE_SIZE" => "70",	// Максимальный размер картинки товара
                    ),
                    false
                );?>
      </a>
    </div>
  </header>
<header class="header header_product">
    <div class="header__left">
      <a href="#" data-hystmodal="#menuModal" class="menu-btn">
        <svg width="21" height="10" viewBox="0 0 21 10" fill="none" xmlns="http://www.w3.org/2000/svg" class="menu-btn__icon svg replaced-svg">
<path d="M1 1.69629H20" stroke="#929292" stroke-width="1.5" stroke-linecap="round"></path>
<path d="M1 8.69629H20" stroke="#929292" stroke-width="1.5" stroke-linecap="round"></path>
</svg>
        <span class="menu-btn__text">Меню</span>
      </a>
      <a href="#" data-hystmodal="#searchModal" class="search-btn">
          <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/search.svg" alt="menu icon" class="search-btn__icon svg">
          <span class="search-btn__text">Поиск</span>
        </a>
    </div>
    <a href="/" class="header__logo logo">
      <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
      <a href="/profile/favorite/" class="header__link">
        <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon svg replaced-svg">
<g clip-path="url(#clip0_291_84)">
<path fill-rule="evenodd" clip-rule="evenodd" d="M4.76985 0.742123C4.74967 0.753839 4.65611 0.781455 4.56193 0.803518C2.66207 1.24836 1.32015 2.29401 0.538952 3.93829C0.484723 4.05245 0.440352 4.15697 0.440352 4.17059C0.440352 4.18422 0.424331 4.23375 0.404713 4.28064C0.385121 4.32756 0.369368 4.36848 0.369711 4.37159C0.370053 4.37472 0.34322 4.45431 0.310052 4.54848C0.199784 4.86177 0.115493 5.24607 0.0339177 5.80739C-0.00920602 6.10404 -0.000742707 7.29488 0.0464415 7.56935C0.364378 9.41904 1.09873 10.6924 2.92201 12.5557C3.77463 13.427 5.11317 14.6134 6.45938 15.691C7.11174 16.2133 7.51749 16.5228 8.53675 17.2758C8.73775 17.4242 9.10431 17.5962 9.33766 17.6516C9.40165 17.6668 9.60665 17.6836 9.79321 17.6889C10.1669 17.6996 10.304 17.6765 10.6452 17.5455C10.9356 17.4341 11.4565 17.0678 12.6461 16.1389C14.1505 14.9641 15.5243 13.7682 16.5739 12.7196C18.2171 11.078 19.0071 9.91581 19.385 8.58445C19.4079 8.50374 19.4361 8.41017 19.4475 8.37654C19.4872 8.26021 19.5924 7.70021 19.6222 7.44629C19.6428 7.27032 19.6573 7.21012 19.669 7.25192C19.6782 7.28524 19.6858 6.99881 19.6858 6.61539C19.6858 6.23175 19.677 5.94576 19.6663 5.97942C19.6477 6.03732 19.6465 6.03631 19.6442 5.9609C19.6346 5.64815 19.4834 4.94095 19.3209 4.44922C19.1451 3.91718 18.7767 3.21806 18.4786 2.85054C18.304 2.63519 17.9831 2.29746 17.7885 2.12423C17.5706 1.93021 17.1086 1.58711 17.0264 1.5582C17.0052 1.55074 16.9713 1.53065 16.9511 1.51353C16.8455 1.42393 16.1889 1.1286 15.8504 1.01843C15.1202 0.780746 14.6452 0.710177 13.8814 0.725905C13.4479 0.734809 13.3517 0.744691 13.0375 0.812618C11.943 1.04915 11.0668 1.52231 10.2812 2.30108C10.0485 2.53177 9.95825 2.60574 9.89475 2.61765C9.76249 2.64245 9.6509 2.59025 9.53511 2.44941C9.18858 2.02791 8.50535 1.51791 7.90078 1.22952C7.6056 1.08873 6.95945 0.879713 6.62884 0.818072C6.47413 0.789233 6.30408 0.756407 6.25093 0.745131C6.1218 0.717711 4.81649 0.715069 4.76985 0.742123ZM5.11229 2.39907C5.07866 2.40533 4.95208 2.42679 4.831 2.44672C4.40979 2.51607 3.79914 2.76664 3.39236 3.03705C2.54463 3.60062 1.98028 4.45561 1.76503 5.50244C1.68304 5.90115 1.65785 6.16253 1.65772 6.61539C1.6575 7.48009 1.82719 8.21711 2.20121 8.97582C2.38855 9.35586 2.81575 9.99068 3.07467 10.2738C3.11239 10.3151 3.20763 10.4251 3.28635 10.5184C3.86677 11.2063 4.85438 12.1739 5.91871 13.0974C6.00398 13.1714 6.08582 13.2433 6.10057 13.2572C6.11534 13.271 6.23747 13.3743 6.37201 13.4867C6.68363 13.747 6.80305 13.8476 6.84899 13.8887C6.93149 13.9625 7.93909 14.7695 8.29215 15.0445C8.79807 15.4387 9.507 15.959 9.61206 16.0133C9.72866 16.0736 9.94215 16.0712 10.0596 16.0082C10.1791 15.9442 10.8812 15.4278 11.4353 14.9964C13.8508 13.116 15.6661 11.4376 16.7432 10.0888C17.4568 9.19508 17.8453 8.30463 17.9811 7.25136C18.0192 6.95576 18.0187 6.26044 17.9801 5.94273C17.8706 5.04092 17.5558 4.30256 17.0145 3.67801C16.6001 3.19988 15.9748 2.79276 15.3123 2.56983C14.947 2.44689 14.6772 2.40355 14.1932 2.39C13.4968 2.3705 13.0326 2.46147 12.4839 2.72489C12.066 2.92551 11.7799 3.12905 11.4109 3.4881C11.236 3.65827 11.0536 3.83099 11.0056 3.87193C10.8513 4.00368 10.5657 4.13955 10.2991 4.20817C10.0693 4.26731 10.0151 4.27252 9.761 4.25995C9.56338 4.25014 9.41897 4.22781 9.28045 4.18559C8.90136 4.07009 8.75626 3.97354 8.31661 3.54429C7.91551 3.15267 7.67643 2.97348 7.297 2.78007C6.7227 2.48733 6.38544 2.40805 5.66265 2.39589C5.39359 2.39137 5.14593 2.39281 5.11229 2.39907ZM0.0108515 6.63985C0.0108515 7.02327 0.0142271 7.18011 0.0183609 6.98841C0.0224947 6.79669 0.0224947 6.48299 0.0183609 6.29129C0.0142271 6.09957 0.0108515 6.25643 0.0108515 6.63985Z" fill="#929292"></path>
</g>
<defs>
<clipPath id="clip0_291_84">
<rect width="19.6662" height="17" fill="white" transform="translate(0 0.696289)"></rect>
</clipPath>
</defs>
</svg>
      </a>
      <a href="/profile/" class="header__link">
        <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon svg replaced-svg">
<path d="M7.50007 10.1963C6.23444 10.1963 5.02 9.69589 4.12503 8.80528C3.23005 7.91467 2.72732 6.70595 2.72732 5.44631C2.72732 4.18667 3.23016 2.97806 4.12503 2.08734C5.01989 1.19663 6.2344 0.696289 7.50007 0.696289C8.76574 0.696289 9.98013 1.19674 10.8751 2.08734C11.7701 2.97795 12.2728 4.18667 12.2728 5.44631C12.2711 6.70591 11.7683 7.91283 10.8734 8.80354C9.97849 9.69417 8.76569 10.1946 7.50007 10.1963ZM7.50007 2.05351C6.59582 2.05351 5.72905 2.4106 5.08982 3.04761C4.44977 3.68377 4.09097 4.54639 4.09097 5.44638C4.09097 6.34636 4.44977 7.20896 5.08982 7.84514C5.72903 8.48215 6.59577 8.83924 7.50007 8.83924C8.40436 8.83924 9.27109 8.48215 9.91031 7.84514C10.5504 7.20898 10.9092 6.34636 10.9092 5.44638C10.9083 4.54728 10.5487 3.68464 9.90946 3.04848C9.27026 2.41232 8.40349 2.05436 7.50007 2.05351ZM7.50007 19.6963C5.39837 19.6954 3.30008 19.508 1.23238 19.1348C0.516488 19.0059 -0.002565 18.3841 9.53502e-06 17.6606C0.00171396 15.8615 0.721032 14.137 1.99944 12.8648C3.27785 11.5925 5.01056 10.8766 6.81819 10.8749H8.18182C9.98949 10.8766 11.7222 11.5925 13.0006 12.8648C14.279 14.1371 14.9983 15.8616 15 17.6606C15.0017 18.3816 14.4852 19.0016 13.7727 19.1322C11.7026 19.5063 9.60355 19.6954 7.50007 19.6963ZM6.81825 12.232C5.37195 12.2337 3.98539 12.8063 2.96343 13.8241C1.94071 14.8411 1.36543 16.2212 1.36373 17.6606C1.36543 17.7259 1.41146 17.7819 1.47538 17.7963C5.46154 18.5122 9.54371 18.5122 13.529 17.7963C13.5929 17.7827 13.6381 17.7259 13.6364 17.6606C13.6347 16.2212 13.0594 14.8412 12.0367 13.8241C11.0148 12.8063 9.62818 12.2337 8.18186 12.232H6.81825Z" fill="#929292"></path>
</svg>
      </a>
            <a href="#" data-hystmodal="#cartModal" class="header__link header__cart">
        <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/cart.svg" alt="cart icon" class="header__link-icon header__link-icon_cart svg">
        <?php $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket", array(
                    "HIDE_ON_BASKET_PAGES" => "N",	// Не показывать на страницах корзины и оформления заказа
                        "PATH_TO_BASKET" => "/cart/",	// Страница корзины
                        "PATH_TO_ORDER" => "/cart/order/",	// Страница оформления заказа
                        "PATH_TO_PERSONAL" => SITE_DIR."personal/",	// Страница персонального раздела
                        "PATH_TO_PROFILE" => SITE_DIR."personal/",	// Страница профиля
                        "PATH_TO_REGISTER" => SITE_DIR."login/",	// Страница регистрации
                        "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
                        "POSITION_HORIZONTAL" => "right",
                        "POSITION_VERTICAL" => "top",
                        "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
                        "SHOW_DELAY" => "N",
                        "SHOW_EMPTY_VALUES" => "N",	// Выводить нулевые значения в пустой корзине
                        "SHOW_IMAGE" => "Y",
                        "SHOW_NOTAVAIL" => "N",
                        "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
                        "SHOW_PERSONAL_LINK" => "N",	// Отображать персональный раздел
                        "SHOW_PRICE" => "Y",
                        "SHOW_PRODUCTS" => "N",	// Показывать список товаров
                        "SHOW_SUMMARY" => "Y",
                        "SHOW_TOTAL_PRICE" => "N",	// Показывать общую сумму по товарам
                        "COMPONENT_TEMPLATE" => "bootstrap_v4",
                        "PATH_TO_AUTHORIZE" => "",	// Страница авторизации
                        "SHOW_REGISTRATION" => "N",	// Добавить возможность регистрации
                        "MAX_IMAGE_SIZE" => "70",	// Максимальный размер картинки товара
                    ),
                    false
                );?>
      </a>
    </div>
  </header>
<?php else:?>
<!-- header -->
<header class="header">
    <div class="header__left">
        <a href="#" data-hystmodal="#menuModal" class="menu-btn">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/menu.svg" alt="menu icon" class="menu-btn__icon svg">
            <span class="menu-btn__text">Меню</span>
        </a>
        <a href="#" data-hystmodal="#searchModal" class="search-btn">
          <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/search.svg" alt="menu icon" class="search-btn__icon svg">
          <span class="search-btn__text">Поиск</span>
        </a>
    </div>
    <a href="/" class="header__logo logo">
        <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
        <a href="/profile/favorite/" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/favorite.svg" alt="favorite icon"
                 class="header__link-icon svg">
        </a>
        <a href="/profile/" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/user.svg" alt="user icon" class="header__link-icon svg">
        </a>
      <a href="#" data-hystmodal="#cartModal" class="header__link header__cart">
        <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/cart.svg" alt="cart icon" class="header__link-icon header__link-icon_cart svg">
        <?php $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "basket", array(
                    "HIDE_ON_BASKET_PAGES" => "N",	// Не показывать на страницах корзины и оформления заказа
                        "PATH_TO_BASKET" => "/cart/",	// Страница корзины
                        "PATH_TO_ORDER" => "/cart/order/",	// Страница оформления заказа
                        "PATH_TO_PERSONAL" => SITE_DIR."personal/",	// Страница персонального раздела
                        "PATH_TO_PROFILE" => SITE_DIR."personal/",	// Страница профиля
                        "PATH_TO_REGISTER" => SITE_DIR."login/",	// Страница регистрации
                        "POSITION_FIXED" => "N",	// Отображать корзину поверх шаблона
                        "POSITION_HORIZONTAL" => "right",
                        "POSITION_VERTICAL" => "top",
                        "SHOW_AUTHOR" => "N",	// Добавить возможность авторизации
                        "SHOW_DELAY" => "N",
                        "SHOW_EMPTY_VALUES" => "N",	// Выводить нулевые значения в пустой корзине
                        "SHOW_IMAGE" => "Y",
                        "SHOW_NOTAVAIL" => "N",
                        "SHOW_NUM_PRODUCTS" => "Y",	// Показывать количество товаров
                        "SHOW_PERSONAL_LINK" => "N",	// Отображать персональный раздел
                        "SHOW_PRICE" => "Y",
                        "SHOW_PRODUCTS" => "N",	// Показывать список товаров
                        "SHOW_SUMMARY" => "Y",
                        "SHOW_TOTAL_PRICE" => "N",	// Показывать общую сумму по товарам
                        "COMPONENT_TEMPLATE" => "bootstrap_v4",
                        "PATH_TO_AUTHORIZE" => "",	// Страница авторизации
                        "SHOW_REGISTRATION" => "N",	// Добавить возможность регистрации
                        "MAX_IMAGE_SIZE" => "70",	// Максимальный размер картинки товара
                    ),
                    false
                );?>
      </a>
    </div>
</header>
<!-- header-end -->
<?php endif;?>
<div class="hystmodal hystmodal_menu" id="menuModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="modal-menu" style="flex-direction: column">
                <ul>
                <li class="h2"><a href="/catalog/">Каталог</a></li>
                    <?php $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top",
                        array(
                            "ALLOW_MULTI_SELECT" => "N",
                            "CHILD_MENU_TYPE" => "left",
                            "COMPONENT_TEMPLATE" => "top",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "Y",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "top",
                            "USE_EXT" => "Y"
                        ),
                        false
                    ); ?>
                </ul>
                <a href="#" data-hystmodal="#searchModal" class="search-btn">
                  <img src="<?=SITE_TEMPLATE_PATH?>/assets/img/search.svg" alt="menu icon" class="search-btn__icon svg">
                  <span class="search-btn__text">Поиск</span>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="hystmodal" id="searchModal" aria-hidden="true">
  <div class="hystmodal__wrap hystmodal_search">
    <div class="hystmodal__window hystmodal__window_search" role="dialog" aria-modal="true">
      <div class="search-modal">
        <p class="search-modal__title">Поиск</p>

        <?php
        $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
            "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
            "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
        ),
        false
        );
        ?>
        <button data-hystclose class="hystmodal__close">Закрыть</button>
      </div>
    </div>
  </div>
</div>
<main <?php if (checkCatalogPath($APPLICATION->GetCurPageParam())):?>class="product-page"<?php endif;?>>
    <?php if (!$isMainPage): ?>
        <?php if ($isAboutPage):?>
            <section class="banner">
              <div class="banner__video video-block">
                <video loop="" muted="" defaultmuted="" playsinline="" autoplay="">
                  <source src="<?=SITE_TEMPLATE_PATH?>/assets/img/banner1.mp4" type="video/mp4">
                </video>
              </div>
              <div class="banner__content banner__content_space">
                <p class="banner__title">
                <?php $APPLICATION->IncludeFile(
                    "/include/about/title_banner.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
                </p>
              </div>
            </section>
        <?php endif;?>
        <?php if(!defined('ERROR_404') && !checkCatalogPath($APPLICATION->GetCurPageParam()) && !$isCartPage&& !$isOrderPage):?>
            <div class="container <?php if (!$isAboutPage):?>top40<?php else:?>top50<?php endif;?>">
                <?php if (!$isServicePages):?>
                <?php $APPLICATION->IncludeComponent(
	"bitrix:breadcrumb", 
	"breadcrumb", 
	[
		"COMPONENT_TEMPLATE" => "breadcrumb",
		"PATH" => "",
		"SITE_ID" => "s1",
		"START_FROM" => "-1"
	],
	false,
	[
		"ACTIVE_COMPONENT" => "N"
	]
); ?>
                <?php endif;?>
                <?php if (!$isAboutPage && !$isProfilePages && !$isCustomersPage && $APPLICATION->GetPageProperty("PRINT_TITLE") === 'N'):?>
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php elseif ($isAboutPage):?>
                <p class="h2" style="margin-top: 35px; margin-bottom: 25px"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php endif;?>
            </div>
        <?php endif; ?>
    <?php endif; ?>