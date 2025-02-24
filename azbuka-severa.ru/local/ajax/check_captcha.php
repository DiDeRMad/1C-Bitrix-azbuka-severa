<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (isset($_POST['token']) && isset($_POST['action'])) {
    $captcha_token = $_POST['token'];
    $captcha_action = $_POST['action'];
} else {
    die('Капча работает некорректно. Обратитесь к администратору!');
}

$url = 'https://www.google.com/recaptcha/api/siteverify';
$params = [
    'secret' => '6Lf6sqcpAAAAAJALtiJroxSqjZI_p5bESfrsE_44',
    'response' => $captcha_token,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

if(!empty($response)) $decoded_response = json_decode($response);

$success = false;
if (
    $decoded_response && $decoded_response->success &&  $decoded_response->score > 0.7
//$decoded_response->success == 1 && $decoded_response->score > 0.7 && $decoded_response
) {
    $success = $decoded_response->success;
    // обрабатываем данные формы, которая защищена капчей
} else {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bot.log', '=====================================', FILE_APPEND);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bot.log', Date('Y-m-d h:i:s'), FILE_APPEND);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bot.log', 'Bot detected!', FILE_APPEND);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bot.log', '=====================================' . PHP_EOL, FILE_APPEND);
}

echo json_encode($success);