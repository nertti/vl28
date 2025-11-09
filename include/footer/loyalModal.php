<?php
/** @var \CMain $APPLICATION */

$arSelect = Array(
    'ID',
    'IBLOCK_ID',
    'NAME',
    'PROPERTY_CONDITIONS',
    'PROPERTY_CONDITIONS_DESCRIPTIONS',
    'PROPERTY_PERIOD',
    'PROPERTY_BONUS',
    'PROPERTY_OPPORTUNITY',
    'PROPERTY_BIRTHDAY',
    );
$arFilter = Array("IBLOCK_ID"=>IntVal(21), "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
$arCards = [];
while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arCards[] = $arFields;
}
?>
<div class="hystmodal" id="loyalModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window hystmodal__window_loyal" role="dialog" aria-modal="true">
            <button data-hystclose="" class="hystmodal__close"></button>
            <div class="loyal">
                <p class="h2">Программа лояльности</p>
                <div class="loyal__table">
                    <div class="loyal__row">
                        <div class="loyal__item">Вид карты</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item">
                                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/card<?=$key+1?>.svg" alt="Карта">
                                <?=$card['NAME']?>
                            </div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Условия получения</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item"><?=$card['PROPERTY_CONDITIONS_DESCRIPTIONS_VALUE']?></div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Срок действия</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item"><?=$card['PROPERTY_PERIOD_VALUE']?></div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Начисление бонусов</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item"><?=$card['PROPERTY_BONUS_VALUE']?> %</div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Возможность 100% оплаты бонусами</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item"><?=$card['PROPERTY_OPPORTUNITY_VALUE']?></div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Бонусы в&nbsp;день рождения</div>
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__item"><?=$card['PROPERTY_BIRTHDAY_VALUE']?></div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__row">
                        <div class="loyal__item">Скидка на&nbsp;доставку из&nbsp;интернет-магазина</div>
                        <div class="loyal__item">Бесплатная доставка</div>
                        <div class="loyal__item">Бесплатная доставка</div>
                        <div class="loyal__item">Бесплатная доставка</div>
                    </div>
                </div>
                <div class="loyal__mobile">
                    <div class="loyal__nav">
                        <div class="loyal__nav-item active">Light</div>
                        <div class="loyal__nav-item">Highlight</div>
                        <div class="loyal__nav-item">Luxury</div>

                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__nav-item<?php if($key == 0):?> active<?php endif;?>"><?=$card['NAME']?></div>
                        <?php endforeach;?>
                    </div>
                    <div class="loyal__tabs">
                        <?php foreach ($arCards as $key => $card):?>
                            <div class="loyal__tab<?php if($key == 0):?> active<?php else:?>  style='display: none;'<?php endif;?>">
                                <div class="loyal__row">
                                    <p>Условия получения</p>
                                    <p><?=$card['PROPERTY_CONDITIONS_DESCRIPTIONS_VALUE']?></p>
                                </div>
                                <div class="loyal__row">
                                    <p>Срок действия</p>
                                    <p><?=$card['PROPERTY_PERIOD_VALUE']?></p>
                                </div>
                                <div class="loyal__row">
                                    <p>Начисление бонусов</p>
                                    <p><?=$card['PROPERTY_BONUS_VALUE']?> %</p>
                                </div>
                                <div class="loyal__row">
                                    <p>Возможность 100% оплаты бонусами</p>
                                    <p><?=$card['PROPERTY_OPPORTUNITY_VALUE']?></p>
                                </div>
                                <div class="loyal__row">
                                    <p>Бонусы в&nbsp;день рождения</p>
                                    <p><?=$card['PROPERTY_BIRTHDAY_VALUE']?></p>
                                </div>
                                <div class="loyal__row">
                                    <p>Скидка на&nbsp;доставку из&nbsp;интернет-магазина</p>
                                    <p>Бесплатная доставка</p>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <p class="loyal__small">* - На один заказ за 7 дней до и 7 дней после дня рождения </p>
                <!--                <a href="#" class="loyal__link">Регламент использования</a>-->
            </div>
        </div>
    </div>
</div>