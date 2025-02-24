<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>

<?php
$APPLICATION->IncludeComponent('fouro:wishlist.list',
    '',
    [
        'IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID
    ])
?>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
Й