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

if($APPLICATION->CaptchaCheckCode($_POST["CAPTCHA_WORD"],$_POST["CAPTCHA_SID"])){
    if(!$_REQUEST['PHONE']) {
        $res['error'] = 'Поле Телефон не заполнено';
    }elseif($_REQUEST['SEC_CODE'] != md5($_REQUEST["IP_ADDRESS"].$_SESSION["BX_SESSION_SIGN"].date("d.m.Y"))){
        $res['error'] = 'Ошибка при проверке вашего браузера. Обратитесь к администратору.';
    }
    else{

        /*$_REQUEST['PHONE'] = str_replace(['(', ')', ' ', '-'], '', $_REQUEST['PHONE']);

        if($_REQUEST['PHONE'][0] == 8)
        {
            $_REQUEST['PHONE'] = preg_replace('/8(.*)/', '+7$1', $_REQUEST['PHONE'], 1);
        }*/
        $_REQUEST['PHONE'] = '+' . NormalizePhone( $_REQUEST['PHONE']);

        global $DB;
        $sql = "SELECT USER_ID FROM b_user_phone_auth WHERE PHONE_NUMBER = '" . mysqli_real_escape_string($DB->db_Conn,$_REQUEST['PHONE']) . "'";

        if ($dbRes = $DB->Query($sql)) {
            $userId = $dbRes->GetNext()['USER_ID'];
        } else {
            $userId = false;
        }

        //if($userId || (!$userId && $_REQUEST['ACTION'] == 'reg')) {

            $res['status'] = 'code';

            //Вытаскиваем шаблон СМС сообщения
            //$rsMess = CEventMessage::GetList($by="site_id", $order="desc", ["TYPE_ID" => "AUTH_CODE_SMS", 'ACTIVE' => 'Y'])->Fetch();
            $rsMess['MESSAGE'] = 'Код подтверждения #CODE#';
            $smsText = '';
            if($rsMess['MESSAGE'])
            {
                $code = mt_rand(1000, 9999);
                $smsText = str_replace('#CODE#', $code, $rsMess['MESSAGE']);

                //Добавляем код в таблицу
                $hlblock = HL\HighloadBlockTable::getById(ID_REGISTRATION_TABLE)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();

                $result = $entity_data_class::add(
                    array(
                        'UF_PHONE' => $_REQUEST['PHONE'],
                        'UF_CODE' => $code,
                        'UF_TIME' => date('d.m.Y H:i:s'),
                        'UF_IP' => $_REQUEST["IP_ADDRESS"],
                    )
                );
                if (!$result->isSuccess())
                {
                    $res['error'] = 'Не удалось получить код';
                }
                else
                {
                    if($smsText)
                    {
                        $smsru = new SMSRU(SMSRU_TOKEN);
                        $data = new stdClass();

                        $data->to = $_REQUEST['PHONE'];
                        $data->text = $smsText;
                        $data->ip = $_REQUEST["IP_ADDRESS"];

                        $sms = $smsru->send_one($data);

                        if ($sms->status != "OK") {
                            Debug::writeToFile("[".date("H:i:s").'] '.$_REQUEST["IP_ADDRESS"].' Ошибка при отправке СМС на номер '.$_REQUEST['PHONE'].': '.$sms->status_text.PHP_EOL,"","log.txt");
                        }
                    }
                }
            }


    //    }
    //    else{
    //        $res['error'] = 'Пользователя с таким номером не существует';
    //    }
    }
}else{
    $res["error"] = "Неверно введено слово с картинки";
    $res["captcha_status"] = 0;
}

header('Content-Type: application/json');
echo json_encode($res, JSON_UNESCAPED_UNICODE);