<?php

/** @var \CMain $APPLICATION */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личные данные");

global $USER;

$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();

CJSCore::Init(array('popup', 'date'));
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
                <div class="account__form-wrap">
                    <form action="/ajax/profile.php" class="account__form top50" id="form">
                        <input type="hidden" value="<?= $userId ?>" name="ID">
                        <div class="account__label">
                            <p class="account__name">Имя</p>
                            <div class="account__inputs">
                                <input type="text" name="NAME" value="<?= $arUser['NAME'] ?>"
                                       class="form-input account__input">
                            </div>
                        </div>
                        <div class="account__label">
                            <p class="account__name">Фамилия</p>
                            <div class="account__inputs">
                                <input type="text" name="LAST_NAME" value="<?= $arUser['LAST_NAME'] ?>"
                                       class="form-input account__input">
                            </div>
                        </div>
                        <div class="account__label">
                            <p class="account__name">Телефон</p>
                            <div class="account__inputs">
                                <input type="text" name="PHONE" value="<?= $arUser['PERSONAL_PHONE'] ?>"
                                       class="form-input phone-input account__input" placeholder="+7">
                            </div>
                        </div>
                        <div class="account__label">
                            <p class="account__name">E-mail</p>
                            <div class="account__inputs">
                                <input type="email" name="EMAIL" value="<?= substr_compare($arUser['EMAIL'], '@vl28.ru', -strlen('@vl28.ru')) ? $arUser['EMAIL'] : ''; ?>"
                                       class="form-input account__input">
                            </div>
                        </div>
                        <div class="account__label">
                            <p class="account__name">Дата рождения</p>
                            <div class="account__inputs" style="position: relative" onclick="BX.calendar({node:this, field:'BIRTHDAY', form: 'form', bTime: false, currentTime: Math.floor(Date.now() / 1000), bHideTime: true});">
                                <input type="text" name="BIRTHDAY" value="<?= $arUser['PERSONAL_BIRTHDAY'] ?>"
                                       class="form-input account__input">
                                <img src="/local/templates/public/assets/img/calendar.svg"
                                     style="
                                     right: 10px;
                                     top: 9px;
                                     position: absolute;
"
                                     alt="Выбрать дату в календаре"
                                     class="position-absolute"
                                     width="28"
                                     height="28"
                                />
                            </div>
                        </div>
                        <div class="account__selects">
                            <div class="account__selects-item">
                                <select name="SIZE">
                                    <option value="" selected="">Размер одежды</option>
                                    <option <?php if ($arUser['UF_SIZE'] == '4'): ?>selected<?php endif; ?> value="4">
                                        XS
                                    </option>
                                    <option <?php if ($arUser['UF_SIZE'] == '5'): ?>selected<?php endif; ?> value="5">
                                        S
                                    </option>
                                    <option <?php if ($arUser['UF_SIZE'] == '6'): ?>selected<?php endif; ?> value="6">
                                        M
                                    </option>
                                    <option <?php if ($arUser['UF_SIZE'] == '7'): ?>selected<?php endif; ?> value="7">
                                        L
                                    </option>
                                    <option <?php if ($arUser['UF_SIZE'] == '8'): ?>selected<?php endif; ?> value="8">
                                        XL
                                    </option>
                                    <option <?php if ($arUser['UF_SIZE'] == '9'): ?>selected<?php endif; ?> value="9">
                                        XXL
                                    </option>
                                </select>
                            </div>
                            <div class="account__selects-item">
                                <select name="GENDER">
                                    <option value="" selected>Пол</option>
                                    <option <?php if ($arUser['PERSONAL_GENDER'] == 'M'): ?>selected<?php endif; ?>
                                            value="M">М
                                    </option>
                                    <option <?php if ($arUser['PERSONAL_GENDER'] == 'F'): ?>selected<?php endif; ?>
                                            value="F">Ж
                                    </option>
                                </select>
                            </div>
                        </div>
                        <input type="submit" id="saveBtn" class="black-btn" value="Сохранить">
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const myModalSuccess = new HystModal({
                                linkAttributeName: 'data-hystmodal',
                            });

                            const form = document.querySelector('#form');
                            const saveBtn = document.querySelector('#saveBtn');
                            // Если форма найдена, добавляем слушатель события submit
                            if (form) {
                                form.addEventListener('submit', handleFormSubmit);
                            } else {
                                console.warn('Форма не найдена на странице');
                            }

                            function handleFormSubmit(event) {
                                event.preventDefault();
                                saveBtn.innerHTML = `
                  <span class='spinner-grow spinner-grow-sm' aria-hidden='true'></span>
                  <span role='status'>Сохраняем...</span>
                `;
                                const formData = new FormData(form);
                                fetch(form.action, {
                                    method: 'POST',
                                    body: formData
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        saveBtn.innerHTML = `Сохранить изменения`;
                                        let info = document.querySelector('#alertModalError .alertText .grey');
                                        //info.innerHTML = ``;
                                        if (data.status === 'error') {
                                            const errorList = Object.entries(data.message || {}).map(([key, value]) =>
                                                `<div class="error-message">${value}</div>`
                                            ).join('');
                                            info.innerHTML = `${errorList}`
                                            myModalSuccess.open('#alertModalError');
                                        } else {
                                            myModalSuccess.open('#alertModal');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Ошибка при отправке формы:', error);
                                    });
                            }
                        })
                        ;
                    </script>
                    <span data-hystmodal="#exitModal" class="account__link account__link_out pointer">Выйти из
                        аккаунта</span>

                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="alertModal" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
                 style="  min-height: auto;">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="alertText">
                    <p class="h2">Данные успешно сохранены!</p>
                </div>
            </div>
        </div>
    </div>
    <div class="hystmodal" id="alertModalError" aria-hidden="true">
        <div class="hystmodal__wrap">
            <div class="hystmodal__window hystmodal__window_subscribe" role="dialog" aria-modal="true"
                 style="  min-height: auto;">
                <button data-hystclose="" class="hystmodal__close"></button>
                <div class="alertText">
                    <p class="h2">Ошибка при сохранении данных!</p>
                    <p class="grey" style="margin-bottom: 0"></p>
                </div>
            </div>
        </div>
    </div>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>