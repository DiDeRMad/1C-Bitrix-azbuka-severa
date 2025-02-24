<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");

if ($_POST['CLIENT_PHONE']) {
    $arValues = [
        "form_text_6" => $_POST['CLIENT_PHONE'],
        "form_text_7" => $_POST['CLIENT_NAME'],
        "form_textarea_8" => $_POST['COMMENT'],
    ];

    if ($RESULT_ID =  CFormResult::Add(3, $arValues)) {
        echo "good";
    }

    $arMailFields = [
        'PHONE' => $_POST['CLIENT_PHONE'],
        'NAME' => $_POST['CLIENT_NAME'],
        'COMMENT' => $_POST['COMMENT'],
    ];

    CEvent::Send("FORM_SEND_3", 's1', $arMailFields);
}