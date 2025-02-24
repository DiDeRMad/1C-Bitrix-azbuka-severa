<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
if(!$APPLICATION->CaptchaCheckCode($_POST["WORD"], $_POST["CODE"])){
	echo "bad";
}else{
	echo "good";
}?>