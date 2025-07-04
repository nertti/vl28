<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="hystmodal" id="filterModal" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <button data-hystclose class="hystmodal__close"></button>
            <div class="filter">
                <a href="#" data-hystclose class="filter__clear">очистить фильтры</a>
                <form id="form" action="/ajax/catalogFilter.php" class="filter__form">
                    <input type="hidden" name="section" id="section" value="<?= $arParams['SECTION_ID'] ?>">
                    <div class="filter__wrap">
                        <?php foreach ($arResult['ITEMS'] as $arItem): ?>
                            <?php if (empty($arItem['DISPLAY_TYPE'])) {
                                continue;
                            } ?>
                            <div class="filter__block">
                                <p class="filter__name"><?= $arItem['NAME'] ?></p>
                                <div class="filter__list <?php if ($arItem['DISPLAY_TYPE'] == 'K'): ?>filter__list_row<?php endif; ?> <?php if ($arItem['NAME'] == 'Размер'): ?>filter_size_list<?php endif; ?>">
                                    <?php //pr($arItem) ?>
                                    <?php foreach ($arItem['VALUES'] as $key => $value): ?>
                                        <?php if ($arItem['DISPLAY_TYPE'] == 'F' && $arItem['CODE'] !== 'COLOR'): ?>
                                            <label class="filter__item">
                                                <input type="checkbox" name="<?= $value['URL_ID'] ?>"
                                                       <?php if ($value['CHECKED'] == 'Y' || $_REQUEST['collection'] == $value['URL_ID']): ?>checked<?php endif; ?>>
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php elseif ($arItem['DISPLAY_TYPE'] == 'H' || $arItem['CODE'] == 'COLOR'): ?>
                                            <label class="filter__item filter__item_color">
                                                <input type="checkbox" name="<?= $value['URL_ID'] ?>"
                                                       <?php if ($value['CHECKED'] == 'Y'): ?>checked<?php endif; ?>>
                                                <span class="checkmark">
                                                    <span class="checkmark__color"
                                                          style="background: <?= $value['URL_ID'] ?>; border: 1px solid #CCC;"></span>
                                                </span>
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php elseif ($arItem['DISPLAY_TYPE'] == 'K'): ?>
                                            <label class="filter__item">
                                                <input type="checkbox" name="<?= $value['URL_ID'] ?>"
                                                       <?php if ($value['CHECKED'] == 'Y'): ?>checked<?php endif; ?>>
                                                <span><?= $value['VALUE'] ?></span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button data-hystclose id="submit_btn" type="submit" class="main-btn">Применить фильтр</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#form');
        const submit_btn = document.querySelector('#submit_btn');
        const clear_btn = document.querySelector('.filter__clear');
        // Если форма найдена, добавляем слушатель события submit
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
            clear_btn.addEventListener('click', handleFormReset);
        } else {
            console.warn('Форма не найдена на странице');
        }

        function sendForm(reset) {
            submit_btn.innerHTML = `
                  <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                  <span role='status'>Фильтруем...</span>
                `;
            let formData;
            if (reset) {
                formData = new FormData();
                formData.append('section', document.querySelector('#section').value);
            } else {
                formData = new FormData(form);
            }
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(data => {
                    submit_btn.innerHTML = `Применить фильтр`;
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = data;
                    const mainSection = tempContainer.querySelector('.products__list');
                    const existingMainSection = document.querySelector('.products__list');

                    if (existingMainSection && mainSection) {
                        existingMainSection.replaceWith(mainSection);
                    }
                    setTimeout(function () {
                        let productSwiperFilter = new Swiper('.product__swiper ', {
                            slidesPerView: 1,
                            loop: true,
                            observer: true,
                            pagination: {
                                el: '.product-swiper .swiper-pagination',
                                clickable: true,
                            },
                        });
                    }, 100);
                })
                .catch(error => {
                    console.error('Ошибка при авторизации:', error);
                });
        }

        function handleFormSubmit(event) {
            event.preventDefault();
            sendForm(false);
        }

        function handleFormReset(event) {
            event.preventDefault();
            sendForm(true);
        }


        const params = new URLSearchParams(window.location.search);
        if (params.has('collection')) {
            sendForm();
        }
    });
</script>
