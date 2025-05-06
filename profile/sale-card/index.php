<?php

/** @var \CMain $APPLICATION */
/** @var $userBonus */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/include/order/bonus.php");

use Bitrix\Main\Loader;

Loader::includeModule("sale");
Loader::includeModule("catalog");
CModule::IncludeModule('iblock');

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();

$rsCard = CUserFieldEnum::GetList(array(), array(
    "ID" => $arUser["UF_CARD"],
));
if ($arCard = $rsCard->GetNext())
    //pr($arCard['VALUE']);
// карта пользователя Тест

$res = CIBlockElement::GetList(
    array('CREATED_DATE' => 'ask'),
    array("IBLOCK_ID" => 11, 'MODIFIED_BY' => $userId),
    false,
    false,
    array('CREATED_DATE')
);

// Выводим элементы
while ($ob = $res->GetNext()) {
    if (!empty($ob['CREATED_DATE'])) {
        $dateTime = DateTimeImmutable::createFromFormat('Y.m.d', $ob['CREATED_DATE']);
        $formattedDate = $dateTime->format('d.m.Y');
        $lastDate = $dateTime->modify('+1 year')->format('d.m.Y');
    }
}


$APPLICATION->SetTitle("Карта лояльности");

?>
<?php

$bunusPrice = 0;
$dbRes = \Bitrix\Sale\Order::getList([
    'select' => [
        '*'
    ],
    'filter' => [
        '=USER_ID' => $USER->GetID(),
        '=STATUS_ID' => 'F',
        '=PAYED' => 'Y',
    ]
]);
while ($item = $dbRes->fetch()) {
    //pr($item);
    $bunusPrice += (int)$item['PRICE']; // потраченная сумма по заказам которые оплатили
}
//pr($bunusPrice);
?>
    <div class="container">
        <div class="account__wrap">
            <div class="account__left">
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>
                <?php $APPLICATION->IncludeComponent("bitrix:menu", "profile", array(
                    "ALLOW_MULTI_SELECT" => "N",    // Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",    // Тип меню для остальных уровней
                    "COMPONENT_TEMPLATE" => "bottom",
                    "DELAY" => "N",    // Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "1",    // Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",    // Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",    // Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "Y",    // Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",    // Учитывать права доступа
                    "ROOT_MENU_TYPE" => "left",    // Тип меню для первого уровня
                    "USE_EXT" => "N",    // Подключать файлы с именами вида .тип_меню.menu_ext.php
                ),
                    false
                ); ?>
            </div>
            <div class="account__right">
                <div class="account__default">
                    <div class="account__card">
                        <p>Ваша карта</p>

                        <div class="account__card-wrap">
                            <div class="account__card-img">
                                <span class="account__card-type"><?= $arCard['VALUE'] ?></span>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card1.svg" alt="Карта">
                                <span class="account__card-count"><?= $userBonus ?> баллов</span>
                            </div>
                            <div class="account__card-params">
                                <p>Уровень карты: <strong><?= $arCard['VALUE'] ?></strong></p>
                                <p>Доступные бонусы: <strong><?= $userBonus ?> баллов</strong></p>
<!--                                <p>Ближайшая дата сгорания бонусов: <strong>--><?php //= $lastDate ?><!--</strong></p>-->
                                <?php if ($bunusPrice > 75000 && $bunusPrice < 149999): ?>
                                    <p>До следующего уровня (Highlight) осталось: <strong><?= $bunusPrice - 75000 ?>
                                            ₽</strong></p>
                                <?php elseif ($bunusPrice > 150000): ?>
                                    <p>До следующего уровня (Luxury) осталось: <strong><?= $bunusPrice - 150000 ?>
                                            ₽</strong></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="account__card-footer">
                            <p>
                                5% от стоимости покупки возвращается на Вашу карту лояльности на 15 день после получения
                                заказа
                            </p>
                            <a href="#" data-hystmodal="#loyalModal" class="black-btn">Подробнее о программе
                                лояльности</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>