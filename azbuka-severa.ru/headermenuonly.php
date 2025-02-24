<?php include($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
    $APPLICATION->IncludeComponent(
        "fouro:menu",
        "",
        array(
            'IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID
        )
    );
?>