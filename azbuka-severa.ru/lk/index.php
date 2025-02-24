<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('Личный кабинет');
    global $USER;
    if (!$USER->IsAuthorized()) {
        if (isset($_GET['logout']) && $_GET['logout'] == "yes") {
            LocalRedirect("/");
        } else {
            LocalRedirect('/');
        }
    }

?>

<?php
    $APPLICATION->IncludeComponent(
        'informula:main.profile',
        'lk',
        [
            "USER_PROPERTY_NAME" => "",
            "SET_TITLE" => "Y",
            "AJAX_MODE" => "N",
            "USER_PROPERTY" => Array(),
            "SEND_INFO" => "N",
            "CHECK_RIGHTS" => "Y",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "AJAX_OPTION_HISTORY" => "N"
        ]
    )
?>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

