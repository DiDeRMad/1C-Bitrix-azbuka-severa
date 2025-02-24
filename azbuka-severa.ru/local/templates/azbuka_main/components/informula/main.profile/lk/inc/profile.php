<?php //echo '<pre>'; print_r($arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES']); echo '</pre>'; ?>
<form method="post" name="form1" action="<?=$arProfileBlock["FORM_TARGET"]?>" enctype="multipart/form-data">
    <?=$arProfileBlock["BX_SESSION_CHECK"]?>
    <input type="hidden" name="lang" value="<?=LANG?>" />
    <input type="hidden" name="ID" value=<?=$arProfileBlock["ID"]?> />
    <div class="row align-items-end">
        <div class="col-lg-3">
            <div class="form-group">
                <label class="form-label">Имя <span class="text-red">*</span></label>
                <input type="text" class="input form-control" name="NAME" required value="<?=$arProfileBlock["arUser"]["NAME"]?>">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="form-label">Фамилия <span class="text-red">*</span></label>
                <input type="text" class="input form-control" name="LAST_NAME" required value="<?=$arProfileBlock["arUser"]["LAST_NAME"]?>">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="form-label">Дата рождения <span class="text-red">*</span></label>
                <input type="text" class="input form-control datepicker" name="UF_BIRTHDATE"  required <?= $arProfileBlock["arUser"]['UF_BIRTHDATE'] != '' ? 'value="' . $arProfileBlock["arUser"]["UF_BIRTHDATE"] . '"' : 'placeholder="дд.мм.гггг"' ?>>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="form-label">Телефон <span class="text-red">*</span></label>
                <input type="text" class="input form-control" name="PERSONAL_PHONE" required value="<?=$arProfileBlock["arUser"]["PERSONAL_PHONE"]?>">
            </div>
        </div>
        <div class="col-lg-9">
            <div class="form-group">
                <label class="form-label">Адрес (основной) <span class="text-red">*</span></label>
                <input type="hidden" name="UF_UF_ADDRESSES_NAMES[]" value="Адрес (основной)" />
                <input type="text" id="address_main" class="input form-control" name="UF_ADDRESSES[]" required value="<?=$arProfileBlock["arUser"]["UF_ADDRESSES"][0]?>">
                <div class="" style="position:absolute; z-index:4000; display:none; width: 90%;" id="address_selector">
                    <select class="" name="" size="10" id="address_selector_select" style=" margin-left: 20px;width: 100%;">
                    </select>
                </div>
            </div>

        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <button type="button" class="btn btn--blue btn_icon_plus" data-modal="true" data-modal-id="#modal-address-add">Добавить адрес</button>
            </div>
        </div>
    </div>
    <div class="row align-items-end">
        <div class="col-lg-6">
            <div class="row align-items-end js-addresses-list">
                <?php foreach ($arProfileBlock['arUser']['UF_ADDRESSES'] as $key => $address) {
                    if ($key == 0) continue; ?>
                <div class="col-lg-6">
                    <div class="form-group">
                        <div class="form-group__tools">
                            <label class="form-label"><?= ($arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] && $arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] != '') ? $arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] : 'Адрес ' . $key ?></label>
                            <a class="form-group__edit icon-link icon-link_edit js-address-edit" href="#" data-modal="true" data-modal-id="#modal-address-add" data-key="<?= $key ?>"></a>
                            <a class="form-group__del icon-link icon-link_close js-address-del" href="#"></a>
                        </div>
                        <input type="hidden" name="UF_UF_ADDRESSES_NAMES[]" value="<?= ($arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] && $arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] != '') ? $arProfileBlock['arUser']['UF_UF_ADDRESSES_NAMES'][$key] : 'Адрес ' . $key ?>" />
                        <input type="text" class="input form-control" name="UF_ADDRESSES[]" value="<?= $address ?>" readonly>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row align-items-end">
        <div class="col-lg-6">
            <input type="submit" name="save" id="save_profile" class="btn btn--blue" value="Сохранить" <?= $arProfileBlock["arUser"]["UF_ADDRESSES"][0] ? '' : ' disabled="" style="background-color:gray;"' ?>/>
        </div>
    </div>
</form>
