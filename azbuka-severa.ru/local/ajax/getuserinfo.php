<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;

$arInfo = [
        'ID' => $USER->IsAuthorized() ? $USER->GetID() : 0,
    'COOKIE' => $_COOKIE['PHPSESSID'] ?: ''
];

echo json_encode($arInfo);