<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

/** @var \CMain $APPLICATION */
/** @var \CMain $USER */

$isMainPage = $APPLICATION->GetCurPage(false) === '/';
$isAboutPage = $APPLICATION->GetCurPage(false) === '/about/';
$isProfilePages = strpos($APPLICATION->GetCurPage(false), '/profile/') !== false;

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

if ($isProfilePages){
    if (!$USER->IsAuthorized()) {
    header('Location: /login/');
    exit();
}
}

?>
<!doctype html>
<html lang="ru">
<head>
    <?php Asset::getInstance()->addString("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>") ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
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
      <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
            "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
            "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
        ),
            false
        ); ?>
    </div>
    <a href="/" class="header__logo logo">
      <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
      <a href="/favorite/" class="header__link">
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
      <a href="/cart/" class="header__link">
        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon header__link-icon_cart svg replaced-svg">
<g clip-path="url(#clip0_291_81)">
<path d="M15.1758 4.68066H2.42578C1.69228 4.68066 1.09766 5.27529 1.09766 6.00879V16.6338C1.09766 17.3673 1.69228 17.9619 2.42578 17.9619H15.1758C15.9093 17.9619 16.5039 17.3673 16.5039 16.6338V6.00879C16.5039 5.27529 15.9093 4.68066 15.1758 4.68066Z" stroke="#929292" stroke-width="1.59375"></path>
<path d="M13.0508 7.07129C13.0508 4.13728 11.148 1.75879 8.80078 1.75879C6.45357 1.75879 4.55078 4.13728 4.55078 7.07129" stroke="#929292" stroke-width="1.59375" stroke-linecap="round"></path>
</g>
<defs>
<clipPath id="clip0_291_81">
<rect width="17" height="18.0625" fill="white" transform="translate(0.300781 0.696289)"></rect>
</clipPath>
</defs>
</svg>
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
      <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
            "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
            "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
        ),
            false
        ); ?>
    </div>
    <a href="/" class="header__logo logo">
      <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/logo.svg" alt="VL28" class="logo__img">
    </a>
    <div class="header__right">
      <a href="/favorite/" class="header__link">
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
      <a href="/cart/" class="header__link">
        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="header__link-icon header__link-icon_cart svg replaced-svg">
<g clip-path="url(#clip0_291_81)">
<path d="M15.1758 4.68066H2.42578C1.69228 4.68066 1.09766 5.27529 1.09766 6.00879V16.6338C1.09766 17.3673 1.69228 17.9619 2.42578 17.9619H15.1758C15.9093 17.9619 16.5039 17.3673 16.5039 16.6338V6.00879C16.5039 5.27529 15.9093 4.68066 15.1758 4.68066Z" stroke="#929292" stroke-width="1.59375"></path>
<path d="M13.0508 7.07129C13.0508 4.13728 11.148 1.75879 8.80078 1.75879C6.45357 1.75879 4.55078 4.13728 4.55078 7.07129" stroke="#929292" stroke-width="1.59375" stroke-linecap="round"></path>
</g>
<defs>
<clipPath id="clip0_291_81">
<rect width="17" height="18.0625" fill="white" transform="translate(0.300781 0.696289)"></rect>
</clipPath>
</defs>
</svg>
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
        <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
            "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
            "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
        ),
            false
        ); ?>
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
        <a href="/cart/" class="header__link">
            <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/cart.svg" alt="cart icon"
                 class="header__link-icon header__link-icon_cart svg">
        </a>
    </div>
</header>
<!-- header-end -->
<?php endif;?>
<div class="hystmodal hystmodal_menu" id="menuModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="modal-menu">
                <ul>
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
                            "USE_EXT" => "N"
                        ),
                        false
                    ); ?>
                </ul>
                <?php $APPLICATION->IncludeComponent("bitrix:search.form", "search", array(
                    "PAGE" => "#SITE_DIR#search/",    // Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
                    "USE_SUGGEST" => "N",    // Показывать подсказку с поисковыми фразами
                ),
                    false
                ); ?>
            </div>
        </div>
    </div>
</div>
<main>
    <?php if ($isMainPage): ?>
        <?php $APPLICATION->IncludeComponent("bitrix:news.list", "main_top_banners", array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
            "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
            "AJAX_MODE" => "N",    // Включить режим AJAX
            "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
            "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
            "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
            "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
            "CACHE_FILTER" => "N",    // Кешировать при установленном фильтре
            "CACHE_GROUPS" => "Y",    // Учитывать права доступа
            "CACHE_TIME" => "360000",    // Время кеширования (сек.)
            "CACHE_TYPE" => "A",    // Тип кеширования
            "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
            "DISPLAY_BOTTOM_PAGER" => "N",    // Выводить под списком
            "DISPLAY_DATE" => "N",    // Выводить дату элемента
            "DISPLAY_NAME" => "Y",    // Выводить название элемента
            "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
            "DISPLAY_PREVIEW_TEXT" => "N",    // Выводить текст анонса
            "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
            "FIELD_CODE" => array(    // Поля
                0 => "",
                1 => "",
            ),
            "FILTER_NAME" => "",    // Фильтр
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
            "IBLOCK_ID" => "1",    // Код информационного блока
            "IBLOCK_TYPE" => "rest_entity",    // Тип информационного блока (используется только для проверки)
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
            "INCLUDE_SUBSECTIONS" => "Y",    // Показывать элементы подразделов раздела
            "MEDIA_PROPERTY" => "",
            "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
            "NEWS_COUNT" => "2",    // Количество новостей на странице
            "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
            "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
            "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
            "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
            "PAGER_TEMPLATE" => "",    // Шаблон постраничной навигации
            "PAGER_TITLE" => "",    // Название категорий
            "PARENT_SECTION" => "1",    // ID раздела
            "PARENT_SECTION_CODE" => "",    // Код раздела
            "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
            "PROPERTY_CODE" => array(    // Свойства
                0 => "TYPE",
                1 => "LINK",
                2 => "ADDITIONAL_LINK",
                3 => "SUBTITLE",
            ),
            "SEARCH_PAGE" => "/search/",
            "SET_BROWSER_TITLE" => "N",    // Устанавливать заголовок окна браузера
            "SET_LAST_MODIFIED" => "N",    // Устанавливать в заголовках ответа время модификации страницы
            "SET_META_DESCRIPTION" => "N",    // Устанавливать описание страницы
            "SET_META_KEYWORDS" => "N",    // Устанавливать ключевые слова страницы
            "SET_STATUS_404" => "N",    // Устанавливать статус 404
            "SET_TITLE" => "N",    // Устанавливать заголовок страницы
            "SHOW_404" => "N",    // Показ специальной страницы
            "SLIDER_PROPERTY" => "",
            "SORT_BY1" => "SORT",    // Поле для первой сортировки новостей
            "SORT_BY2" => "ACTIVE_FROM",    // Поле для второй сортировки новостей
            "SORT_ORDER1" => "ASC",    // Направление для первой сортировки новостей
            "SORT_ORDER2" => "DESC",    // Направление для второй сортировки новостей
            "STRICT_SECTION_CHECK" => "Y",    // Строгая проверка раздела для показа списка
            "USE_RATING" => "N",
            "USE_SHARE" => "N"
        ), false); ?>
        <section class="products">
            <p class="h2">Новые поступления</p>
            <div class="products__list products__list_home">
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product1">
                        <div class="swiper-wrapper" id="swiper-wrapper-a03ca10111a1108a3" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product1.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product1.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-a03ca10111a1108a3">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-a03ca10111a1108a3">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">T-SHIRT DENSE BLACK 100</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product2">
                        <div class="swiper-wrapper" id="swiper-wrapper-58b105766a77a4457" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product2.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product2.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-58b105766a77a4457">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-58b105766a77a4457">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">T-SHIRT DENSE WHITE</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
                <a href="#" class="product">
                    <div class="product__swiper swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                         id="product3">
                        <div class="swiper-wrapper" id="swiper-wrapper-b8e61fd49678fb54" aria-live="polite">
                            <div class="swiper-slide swiper-slide-active" style="width: 634px;" role="group"
                                 aria-label="1 / 2" data-swiper-slide-index="0">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product3.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 634px;" role="group"
                                 aria-label="2 / 2" data-swiper-slide-index="1">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/export/product3.webp"
                                     alt="T-SHIRT DENSE BLACK 100" class="product__img">
                            </div>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-b8e61fd49678fb54">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-left"></use>
                            </svg>
                        </div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-b8e61fd49678fb54">
                            <svg class="arrow">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/arrows.svg#arrow-right"></use>
                            </svg>
                        </div>
                        <div class="swiper-scrollbar swiper-scrollbar-horizontal">
                            <div class="swiper-scrollbar-drag"
                                 style="transform: translate3d(0px, 0px, 0px); width: 0px;"></div>
                        </div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                    <div class="product__inner">
                        <p class="product__title">ZIP-HOODIE</p>
                        <p class="product__price">8 900 ₽</p>
                    </div>
                </a>
            </div>
            <a href="#" class="products__link link">Перейти в каталог</a>
        </section>
        <section class="content">
            <?php $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "main_second_banners",
                array(
                    "ACTIVE_DATE_FORMAT" => "d.m.Y",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "CACHE_TIME" => "360000",
                    "CACHE_TYPE" => "A",
                    "CHECK_DATES" => "Y",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "DETAIL_URL" => "",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "DISPLAY_DATE" => "N",
                    "DISPLAY_NAME" => "Y",
                    "DISPLAY_PICTURE" => "Y",
                    "DISPLAY_PREVIEW_TEXT" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "FIELD_CODE" => array(
                        0 => "",
                        1 => "",
                    ),
                    "FILTER_NAME" => "",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                    "IBLOCK_ID" => "1",
                    "IBLOCK_TYPE" => "rest_entity",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "MEDIA_PROPERTY" => "",
                    "MESSAGE_404" => "",
                    "NEWS_COUNT" => "2",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_TEMPLATE" => "",
                    "PAGER_TITLE" => "",
                    "PARENT_SECTION" => "2",
                    "PARENT_SECTION_CODE" => "",
                    "PREVIEW_TRUNCATE_LEN" => "",
                    "PROPERTY_CODE" => array(
                        0 => "TYPE",
                        1 => "LINK",
                        2 => "SUBTITLE",
                        3 => "ADDITIONAL_LINK",
                        4 => "",
                    ),
                    "SEARCH_PAGE" => "/search/",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_STATUS_404" => "N",
                    "SET_TITLE" => "N",
                    "SHOW_404" => "N",
                    "SLIDER_PROPERTY" => "",
                    "SORT_BY1" => "SORT",
                    "SORT_BY2" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "ASC",
                    "SORT_ORDER2" => "DESC",
                    "STRICT_SECTION_CHECK" => "Y",
                    "USE_RATING" => "N",
                    "USE_SHARE" => "N",
                    "COMPONENT_TEMPLATE" => "main_second_banners"
                ),
                false
            ); ?>
            <div class="container">
                <?php $APPLICATION->IncludeFile(
                    "/include/main/under_second_banners.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
            </div>
        </section>
        <section class="content">
            <?php $APPLICATION->IncludeFile(
                "/include/main/single_banner.php",
                array(),
                array(
                    "MODE" => "text"
                )
            ); ?>
            <div class="container">
                <?php $APPLICATION->IncludeFile(
                    "/include/main/under_single_banner.php",
                    array(),
                    array(
                        "MODE" => "text"
                    )
                ); ?>
            </div>
        </section>
        <section class="blog">
          <?php $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"main_news", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "360000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "3",
		"IBLOCK_TYPE" => "rest_entity",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "Y",
		"MEDIA_PROPERTY" => "",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "3",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_TITLE" => "",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "SOURCE",
			1 => "",
			2 => "",
			3 => "",
			4 => "",
			5 => "",
		),
		"SEARCH_PAGE" => "/search/",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SLIDER_PROPERTY" => "",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "Y",
		"USE_RATING" => "N",
		"USE_SHARE" => "N",
		"COMPONENT_TEMPLATE" => "main_news"
	),
	false
); ?>
        </section>
    <?php else:?>
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
        <?php if(!defined('ERROR_404')):?>
            <div class="container top40">
                <?php $APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "breadcrumb",
                    array(
                        "COMPONENT_TEMPLATE" => "breadcrumb",
                        "PATH" => "",
                        "SITE_ID" => "s1",
                        "START_FROM" => "-1"
                    ),
                    false
                ); ?>
                <?php if ($isAboutPage):?>
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php endif;?>
            </div>
        <?php endif; ?>
    <?php endif; ?>