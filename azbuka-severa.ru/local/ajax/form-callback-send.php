<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");

if ($_POST['CLIENT_PHONE']) {
    $arValues = [
        "form_text_3" => $_POST['CLIENT_PHONE'],
        "form_text_4" => $_POST['CLIENT_NAME'],
        "form_textarea_5" => $_POST['COMMENT'],
    ];

    if ($RESULT_ID =  CFormResult::Add(2, $arValues)) {
        echo "good";
    }

    $arMailFields = [
        'PHONE' => $_POST['CLIENT_PHONE'],
        'NAME' => $_POST['CLIENT_NAME'],
        'COMMENT' => $_POST['COMMENT'],
    ];

    CEvent::Send("FORM_SEND_2", 's1', $arMailFields);

}