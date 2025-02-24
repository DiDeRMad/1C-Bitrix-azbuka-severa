<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\EventManager;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
Loader::includeModule("highloadblock");
\Bitrix\Main\Loader::includeModule("iblock");

global $USER;


if(!$_REQUEST['PHONE'] || !$_REQUEST['AUTH_CODE']) {
    $res['error'] = 'Недостаточно данных';
}
else{
    $phoneSave = $_REQUEST['PHONE'];

    /*$_REQUEST['PHONE'] = str_replace(['(', ')', ' ', '-'], '', $_REQUEST['PHONE']); // Для SMS убрать "+"
    if($_REQUEST['PHONE'][0] == 8)
    {
        $_REQUEST['PHONE'] = preg_replace('/8(.*)/', '7$1', $_REQUEST['PHONE'], 1);
    }*/
    $_REQUEST['PHONE'] = '+' . NormalizePhone( $_REQUEST['PHONE']);



    global $DB;

    $sql = "SELECT USER_ID FROM b_user_phone_auth WHERE PHONE_NUMBER = '" . mysqli_real_escape_string($DB->db_Conn, $_REQUEST['PHONE']) . "'";

    if ($dbRes = $DB->Query($sql)) {
        $userId = $dbRes->GetNext()['USER_ID'];
    } else {
        $userId = false;
    }
   // if($userId || (!$userId && $_REQUEST['ACTION'] == 'reg')) {

        //Проверяем код подтверждения
        $hlblock = HL\HighloadBlockTable::getById(ID_REGISTRATION_TABLE)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $rsData = $entity_data_class::getList(array(
            "select" => ["*", 'UF_*'],
            "order" => ["UF_TIME" => "DESC"],
            "filter" => ["UF_PHONE" => $_REQUEST['PHONE']]  // Задаем параметры фильтра выборки
        ))->Fetch();

        if($rsData['UF_CODE'] && $rsData['UF_CODE'] == $_REQUEST['AUTH_CODE'] && $rsData['UF_TIME'])
        {
            $code_time = strtotime($rsData['UF_TIME'].' +300 sec');
            $time_finish = strtotime('now');
            if($time_finish > $code_time)
            {
                $res['error'] = 'Код недействителен';
            }
            else {

                if (!$userId) {
                    $arFields = [
                        'PHONE_NUMBER' => '+' . NormalizePhone($phoneSave),
                        'PASSWORD' => '123Pass321Word',
                        'CONFIRM_PASSWORD' => '123Pass321Word',
                        'ACTIVE' => 'Y',
                        'LOGIN' => '+' . NormalizePhone($phoneSave)
                    ];

                    $userId = $USER->Add($arFields);
                }

                //$remember = false;
                //if ($_REQUEST['REMEMBER'] && $_REQUEST['REMEMBER'] == "Y") $remember = true;
                if ($userId) {
                    $remember = true;
                    global $USER;
                    $USER->Authorize($userId, $remember);
                    $res['status'] = 'ok';
                } else {
                    $res['error'] = 'Ошибка авторизации' . $USER->LAST_ERROR;
                }
            }
        }
        else{
            $res['error'] = 'Код недействителен ' . $_REQUEST['PHONE'];
        }
   // }


}

header('Content-Type: application/json');
echo json_encode($res, JSON_UNESCAPED_UNICODE);