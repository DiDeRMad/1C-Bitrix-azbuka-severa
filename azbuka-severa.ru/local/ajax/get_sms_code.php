<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");
Loader::includeModule("iblock");

$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
$recaptcha_secret = "6LeyEqwpAAAAAJmCh5X_sePuouYzJBBJ9joyHZLP";
$recaptcha_response = $_POST['recaptcha_response'];

// Отправка запроса с помощью cURL
$data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response,
];

$ch = curl_init($recaptcha_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$result = curl_exec($ch);

if ($result === false) {
    Debug::writeToFile('cURL error: ' . curl_error($ch), "", "log.txt");
    $res["error"] = "Не удалось соединиться с reCAPTCHA. Попробуйте еще раз.";
    $res["captcha_status"] = 0;
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}

curl_close($ch);

// Разбор ответа reCAPTCHA
$responseData = json_decode($result);
Debug::writeToFile('Raw reCAPTCHA response: ' . json_encode($responseData), "", "log.txt");

if ($responseData->success && $responseData->score >= 0.5 && $responseData->action == 'submit') {
    if (!$_REQUEST['PHONE']) {
        $res['error'] = 'Поле Телефон не заполнено';
    } elseif ($_REQUEST['SEC_CODE'] != md5($_REQUEST["IP_ADDRESS"] . $_SESSION["BX_SESSION_SIGN"] . date("d.m.Y"))) {
        $res['error'] = 'Ошибка при проверке вашего браузера. Обратитесь к администратору.';
    } else {
        $_REQUEST['PHONE'] = '+' . NormalizePhone($_REQUEST['PHONE']);
        
        global $DB;
        $sql = "SELECT USER_ID FROM b_user_phone_auth WHERE PHONE_NUMBER = '" . mysqli_real_escape_string($DB->db_Conn, $_REQUEST['PHONE']) . "'";
        $dbRes = $DB->Query($sql);
        $userId = $dbRes->GetNext()['USER_ID'] ?? false;

        $res['status'] = 'code';
        $rsMess['MESSAGE'] = 'Код подтверждения #CODE#';
        
        if ($rsMess['MESSAGE']) {
            $code = mt_rand(1000, 9999);
            $smsText = str_replace('#CODE#', $code, $rsMess['MESSAGE']);
            
            $hlblock = HL\HighloadBlockTable::getById(ID_REGISTRATION_TABLE)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $result = $entity_data_class::add([
                'UF_PHONE' => $_REQUEST['PHONE'],
                'UF_CODE' => $code,
                'UF_TIME' => date('d.m.Y H:i:s'),
                'UF_IP' => $_REQUEST["IP_ADDRESS"],
            ]);

            if ($result->isSuccess()) {
                $smsru = new SMSRU(SMSRU_TOKEN);
                $data = new stdClass();
                $data->to = $_REQUEST['PHONE'];
                $data->text = $smsText;
                $data->ip = $_REQUEST["IP_ADDRESS"];
                $sms = $smsru->send_one($data);

                if ($sms->status != "OK") {
                    Debug::writeToFile("[" . date("H:i:s") . '] Ошибка при отправке СМС на номер ' . $_REQUEST['PHONE'] . ': ' . $sms->status_text, "", "log.txt");
                }
            } else {
                $res['error'] = 'Не удалось получить код';
            }
        }
    }
} else {
    $res["error"] = "Ошибка каптчи, попробуйте еще раз";
    $res["captcha_status"] = 0;
}

header('Content-Type: application/json');
echo json_encode($res, JSON_UNESCAPED_UNICODE);
?>
