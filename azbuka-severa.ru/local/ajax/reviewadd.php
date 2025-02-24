<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader as Loader;

global $USER;
Loader::includeModule('iblock');

if(!empty($_POST)) {
    $el = new CIBlockElement;
    $revName = $USER->GetByID($USER->GetId())->Fetch()['NAME'] ?? 'Новый пользователь #' . $USER->GetId();
    $elid = $_POST['elemid'];
    $arFields = [
        'NAME' => $revName,
        'IBLOCK_ID' => '14',
        'PREVIEW_TEXT' => $_POST['textreview'],
        'CREATED_BY' => $USER->GetId(),
        'PROPERTY_VALUES' => [
            'PRODUCT' => $elid,
            'SHOW_DATE' => date('d.m.Y H:i')
        ]
    ];
    $res = $el->add($arFields);
    if ($res) {
        echo json_encode(['NAME'=>$revName,'TEXT'=>$_POST['textreview'],'DATE_CREATED'=>strtolower(formatdate("d F",maketimestamp(date('d.m.Y'))))]);
    }
}