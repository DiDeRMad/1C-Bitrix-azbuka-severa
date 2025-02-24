<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");

if ($_POST['phone']) {
    $arValues = [
        "form_text_1" => $_POST['phone'],
        "form_text_2" => $_SERVER['REMOTE_ADDR'],
    ];

    if ($RESULT_ID =  CFormResult::Add(1, $arValues)) {
        echo "good";
    }

    $arMailFields = [
        'PHONE' => $_POST['phone'],
        'IP' => $_SERVER['REMOTE_ADDR']
    ];

    CEvent::Send("FORM_SEND_1", 's1', $arMailFields);
}