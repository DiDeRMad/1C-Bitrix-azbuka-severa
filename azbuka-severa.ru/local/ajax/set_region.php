<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->set_cookie("USER_DOMAIN", $_GET["domain"]);
if ($_GET["region"] != "main") {
    $vhost = $_GET["region"].".azbuka-severa.ru";
} else {
    $vhost = "azbuka-severa.ru";
}
echo json_encode(array("success" => "Y", "vhost" => $vhost));
?>
