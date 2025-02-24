<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!empty($arResult)) { ?>
    <div class="container">
        <div class="lk-header">
            <div class="lk-header__icon">
                <svg class="lk-header__icon-svg" width="59" height="60" viewBox="0 0 59 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M29.5 5.80641C15.93 5.80641 4.91663 16.8197 4.91663 30.3897C4.91663 43.9597 15.93 54.9731 29.5 54.9731C43.07 54.9731 54.0833 43.9597 54.0833 30.3897C54.0833 16.8197 43.07 5.80641 29.5 5.80641ZM29.5 13.1814C33.5808 13.1814 36.875 16.4756 36.875 20.5564C36.875 24.6372 33.5808 27.9314 29.5 27.9314C25.4191 27.9314 22.125 24.6372 22.125 20.5564C22.125 16.4756 25.4191 13.1814 29.5 13.1814ZM29.5 48.0897C23.3541 48.0897 17.9212 44.9431 14.75 40.1739C14.8237 35.2818 24.5833 32.6022 29.5 32.6022C34.392 32.6022 44.1762 35.2818 44.25 40.1739C41.0787 44.9431 35.6458 48.0897 29.5 48.0897Z" fill="#12266B"/>
                </svg>
            </div>
            <div class="lk-header__title">Здравствуйте, <?= $arResult['PROFILE']['arUser']['NAME'] ?>!</div>
        </div>
        <?php foreach ($arResult as $profileBlockCode => $arProfileBlock) { ?>
            <div class="collapse-block">
                <div class="collapse-block__header js-collapse-block__header <?= ($profileBlockCode == 'REVIEWS' || $profileBlockCode == 'RECIEPTS') ? 'js-with-slick' : '' ?>">
                    <div class="collapse-block__icon">
                        <?php switch ($profileBlockCode) {
                            case 'PROFILE': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 12.5C14.21 12.5 16 10.71 16 8.5C16 6.29 14.21 4.5 12 4.5C9.79 4.5 8 6.29 8 8.5C8 10.71 9.79 12.5 12 12.5ZM12 14.5C9.33 14.5 4 15.84 4 18.5V20.5H20V18.5C20 15.84 14.67 14.5 12 14.5Z" fill="#12266B"/>
                                </svg>
                            <?php break;
                            case 'ORDER_HISTORY': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2.5H6C4.9 2.5 4.01 3.4 4.01 4.5L4 20.5C4 21.6 4.89 22.5 5.99 22.5H18C19.1 22.5 20 21.6 20 20.5V8.5L14 2.5ZM16 18.5H8V16.5H16V18.5ZM16 14.5H8V12.5H16V14.5ZM13 9.5V4L18.5 9.5H13Z" fill="#12266B"/>
                                </svg>
                            <?php break;
                            case 'FAV': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 21.85L10.55 20.53C5.4 15.86 2 12.78 2 9C2 5.92 4.42 3.5 7.5 3.5C9.24 3.5 10.91 4.31 12 5.59C13.09 4.31 14.76 3.5 16.5 3.5C19.58 3.5 22 5.92 22 9C22 12.78 18.6 15.86 13.45 20.54L12 21.85Z" fill="#12266B"/>
                                </svg>
                                <?php break;
                            case 'SUBSCRIPTIONS': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 8.5V7.5L18 9.5L15 7.5V8.5L18 10.5L21 8.5ZM22 3.5H2C0.9 3.5 0 4.4 0 5.5V19.5C0 20.6 0.9 21.5 2 21.5H22C23.1 21.5 23.99 20.6 23.99 19.5L24 5.5C24 4.4 23.1 3.5 22 3.5ZM8 6.5C9.66 6.5 11 7.84 11 9.5C11 11.16 9.66 12.5 8 12.5C6.34 12.5 5 11.16 5 9.5C5 7.84 6.34 6.5 8 6.5ZM14 18.5H2V17.5C2 15.5 6 14.4 8 14.4C10 14.4 14 15.5 14 17.5V18.5ZM22 12.5H14V6.5H22V12.5Z" fill="#12266B"/>
                                </svg>
                                <?php break;
                            case 'REVIEWS': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 2.5H4C2.9 2.5 2.01 3.4 2.01 4.5L2 22.5L6 18.5H20C21.1 18.5 22 17.6 22 16.5V4.5C22 3.4 21.1 2.5 20 2.5ZM6 14.5V12.03L12.88 5.15C13.08 4.95 13.39 4.95 13.59 5.15L15.36 6.92C15.56 7.12 15.56 7.43 15.36 7.63L8.47 14.5H6ZM18 14.5H10.5L12.5 12.5H18V14.5Z" fill="#12266B"/>
                                </svg>
                                <?php break;
                            case 'RECIEPTS': ?>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 5.5C19.89 5.15 18.67 5 17.5 5C15.55 5 13.45 5.4 12 6.5C10.55 5.4 8.45 5 6.5 5C4.55 5 2.45 5.4 1 6.5V21.15C1 21.4 1.25 21.65 1.5 21.65C1.6 21.65 1.65 21.6 1.75 21.6C3.1 20.95 5.05 20.5 6.5 20.5C8.45 20.5 10.55 20.9 12 22C13.35 21.15 15.8 20.5 17.5 20.5C19.15 20.5 20.85 20.8 22.25 21.55C22.35 21.6 22.4 21.6 22.5 21.6C22.75 21.6 23 21.35 23 21.1V6.5C22.4 6.05 21.75 5.75 21 5.5ZM21 19C19.9 18.65 18.7 18.5 17.5 18.5C15.8 18.5 13.35 19.15 12 20V8.5C13.35 7.65 15.8 7 17.5 7C18.7 7 19.9 7.15 21 7.5V19Z" fill="#12266B"/>
                                    <path d="M17.5 11C18.38 11 19.23 11.09 20 11.26V9.74C19.21 9.59 18.36 9.5 17.5 9.5C15.8 9.5 14.26 9.79 13 10.33V11.99C14.13 11.35 15.7 11 17.5 11Z" fill="#12266B"/>
                                    <path d="M13 12.99V14.65C14.13 14.01 15.7 13.66 17.5 13.66C18.38 13.66 19.23 13.75 20 13.92V12.4C19.21 12.25 18.36 12.16 17.5 12.16C15.8 12.16 14.26 12.46 13 12.99Z" fill="#12266B"/>
                                    <path d="M17.5 14.83C15.8 14.83 14.26 15.12 13 15.66V17.32C14.13 16.68 15.7 16.33 17.5 16.33C18.38 16.33 19.23 16.42 20 16.59V15.07C19.21 14.91 18.36 14.83 17.5 14.83Z" fill="#12266B"/>
                                </svg>
                            <?php break;
                         } ?>
                    </div>
                    <div class="collapse-block__name"><?= GetMessage($profileBlockCode) ?></div>
                </div>
                <div class="collapse-block__body">
                    <?php
                    /*
                     * TODO:
                     * 7) Спросить у Андрея по верстке - на большом экране разваливается
                     */ ?>
                    <?php include("inc/" . strtolower($profileBlockCode) . ".php"); ?>
                </div>
            </div>
        <?php } ?>

        <a href="<?echo $APPLICATION->GetCurPageParam("logout=yes&".bitrix_sessid_get(), ["login",
                "logout",
                "register",
                "forgot_password",
                "change_password"]
        );?>" class="collapse-block collapse-block--link">
            <span class="collapse-block__header">
                <span class="collapse-block__icon">
                    <svg fill="#12266B" width="24" height="18" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.207 9H5V7h7.136L11.05 5.914 12.464 4.5 16 8.036l-3.536 3.535-1.414-1.414L12.207 9zM10 4H8V2H2v12h6v-2h2v4H0V0h10v4z" fill-rule="evenodd"/>
                    </svg>
                </span>
                <span class="collapse-block__name">Выйти</span>
            </span>
        </a>
    </div>
<?php } ?>

<div class="modal modal-default" id="modal-address-add">
    <div class="modal-wrapper">
        <h2 class="modal__caption modal__caption_with_button caption--h3">
            Адрес
            <a class="icon-link icon-link_edit js-modal-address-edit" href="#"></a>
        </h2>
        <form action="" id="address-form">
            <div class="row hidden align-items-center" id="modal-address-name">
                <div class="col form-group">
                    <input type="text" class="input form-control" value="Адрес" name="address_name" id="address_name">
                </div>
                <div class="col-auto form-group">
                    <a class="icon-link icon-link_check js-modal-address-confirm" href="#"></a>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label class="form-label">Город</label>
                        <div class="select-custom__wrapper input form-control pd0">
                            <select class="select-custom" name="town">
                                <option value="Москва">Москва</option>
                                <option value="Санкт-Петербург">Санкт-Петербург</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7">
                    <div class="form-group">
                        <label class="form-label">Адрес</label>
                        <input type="text" class="input form-control" placeholder="Введите улицу" required name="street">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label">Дом, строение, корпус</label>
                        <input type="text" class="input form-control" placeholder="" required name="home">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="form-label">Подъезд</label>
                        <input type="text" class="input form-control" placeholder="" required name="entrance">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="form-label">Этаж</label>
                        <input type="text" class="input form-control" placeholder="" required name="floor">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="form-group">
                        <label class="form-label">Квартира</label>
                        <input type="text" class="input form-control" placeholder="" required name="kv">
                    </div>
                </div>
            </div>
            <button class="btn btn--blue">Сохранить</button>
        </form>
    </div>
</div>
