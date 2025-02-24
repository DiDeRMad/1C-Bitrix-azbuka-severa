<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");

if ($_POST['CLIENT_PHONE']) {
    $arValues = [
        "form_text_13" => $_POST['CLIENT_PHONE'],
        "form_text_14" => $_POST['CLIENT_NAME'],
        "form_text_16" => $_POST['ITEM'],
        "form_textarea_15" => $_POST['COMMENT'],
    ];

    if ($RESULT_ID =  CFormResult::Add(5, $arValues)) {
        echo "good";

        $dbElements = CIBlockElement::GetList([], ['IBLOCK_ID' => '', 'ID' => $_POST['ITEM']], false, false, ['IBLOCK_ID', 'ID', 'NAME']);
        if ($arElem = $dbElements->fetch()) {
            $arMailFields = [
                'PHONE' => $_POST['CLIENT_PHONE'],
                'NAME' => $_POST['CLIENT_NAME'],
                'COMMENT' => $_POST['COMMENT'],
                'ITEM' => '[' . $arElem['ID'] . '] ' . $arElem['NAME']
            ];

            CEvent::Send("FORM_SEND_5", 's1', $arMailFields);
            //CEvent::Send("FORM_FILLING_SIMPLE_FORM_5", 's1', $arMailFields);
        }
    }

}