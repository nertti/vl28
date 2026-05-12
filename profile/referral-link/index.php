<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

/** @var \CMain $APPLICATION */
$APPLICATION->SetTitle("Реферальная ссылка");
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;
global $USER;

$userId = $USER->GetID();
$userReferralLink = UserTable::getList([
        'select' => ['UF_REFERRAL_LINK'],
        'filter' => ['=ID' => $userId],
])->fetch();
?>
    <div class="container">
        <div class="account__wrap">
            <div class="account__left">
                <p class="h2"><?php $APPLICATION->ShowTitle(); ?></p>

                <?php $APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "profile",
                    array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "left",
                        "COMPONENT_TEMPLATE" => "profile",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "1",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_TYPE" => "N",
                        "MENU_CACHE_USE_GROUPS" => "Y",
                        "ROOT_MENU_TYPE" => "left",
                        "USE_EXT" => "N"
                    ),
                    false
                ); ?>
            </div>
            <div class="account__right">
                <div class="account__form-wrap">
                    <div class="account__form top100">
                        <div class="account__label">
                            <label for="link" class="account__name">Ваша ссылка</label>
                            <div class="account__inputs">
                                <input type="text" readonly name="link" id="link" value="https://vl28.pro/?referral_link=<?=$userReferralLink['UF_REFERRAL_LINK']?>" class="form-input account__input">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>