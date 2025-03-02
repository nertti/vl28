<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php /*
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get">
	<?foreach($arResult["ITEMS"] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem["INPUT"];
		endif;
	endforeach;?>
	<table class="data-table" cellspacing="0" cellpadding="2">
	<thead>
		<tr>
			<td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
		</tr>
	</thead>
	<tbody>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<tr>
					<td valign="top"><?=$arItem["NAME"]?>:</td>
					<td valign="top"><?=$arItem["INPUT"]?></td>
				</tr>
			<?endif?>
		<?endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" /><input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></td>
		</tr>
	</tfoot>
	</table>
</form>
 */?>
<div class="hystmodal" id="filterModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="filter">
                <a href="#" class="filter__clear">очистить фильтры</a>
                <form action="#" class="filter__form">
                    <div class="filter__wrap">
                        <div class="filter__block">
                            <p class="filter__name">Коллеция</p>
                            <div class="filter__list">
                                <label class="filter__item">
                                    <input type="checkbox" name="collections" value="Повседневная одежда">
                                    <span>Повседневная одежда</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="collections" value="Мотоэкипировка">
                                    <span>Мотоэкипировка</span>
                                </label>
                            </div>
                        </div>
                        <div class="filter__block">
                            <p class="filter__name">Цвет</p>
                            <div class="filter__list">
                                <label class="filter__item filter__item_color">
                                    <input type="checkbox" name="color" value="Чёрный">
                                    <span class="checkmark">
                      <span class="checkmark__color" style="background: #000;"></span>
                    </span>
                                    <span>Чёрный</span>
                                </label>
                                <label class="filter__item filter__item_color">
                                    <input type="checkbox" name="color" value="Графит">
                                    <span class="checkmark">
                      <span class="checkmark__color" style="background: #837F8E;"></span>
                    </span>
                                    <span>Графит</span>
                                </label>
                                <label class="filter__item filter__item_color">
                                    <input type="checkbox" name="color" value="Серый">
                                    <span class="checkmark">
                      <span class="checkmark__color" style="background: #929292;"></span>
                    </span>
                                    <span>Серый</span>
                                </label>
                                <label class="filter__item filter__item_color">
                                    <input type="checkbox" name="color" value="Белый">
                                    <span class="checkmark">
                      <span class="checkmark__color" style="background: #FFF; border: 1px solid #CCC;"></span>
                    </span>
                                    <span>Белый</span>
                                </label>
                            </div>
                        </div>
                        <div class="filter__block">
                            <p class="filter__name">Материал</p>
                            <div class="filter__list">
                                <label class="filter__item">
                                    <input type="checkbox" name="material" value="Хлопок">
                                    <span>Хлопок</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="material" value="Кашемир">
                                    <span>Кашемир</span>
                                </label>
                            </div>
                        </div>
                        <div class="filter__block">
                            <p class="filter__name">Размер</p>
                            <div class="filter__list filter__list_row">
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="XS" disabled>
                                    <span>XS</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="S">
                                    <span>S</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="M">
                                    <span>M</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="L">
                                    <span>L</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="XL" disabled>
                                    <span>XL</span>
                                </label>
                                <label class="filter__item">
                                    <input type="checkbox" name="size" value="XXL" disabled>
                                    <span>XXL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="submit" class="main-btn" value="Применить фильтры">
                </form>
            </div>
        </div>
    </div>
</div>
