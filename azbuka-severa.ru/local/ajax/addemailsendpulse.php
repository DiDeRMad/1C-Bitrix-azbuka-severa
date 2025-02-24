<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_POST['email']) {
    $data['emails'][] = htmlspecialcharsbx($_POST['email']);

    return \Dev\SendPulse\AddressBookController::addEmail($data);
}