<?php
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
?>
<div class="container">
    <div class="container">
        <?$APPLICATION->IncludeComponent(
            "bitrix:sale.personal.section",
            "main_profile",
            Array(
                "ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS" => array("0"),
                "ACCOUNT_PAYMENT_PERSON_TYPE" => "1",
                "ACCOUNT_PAYMENT_SELL_CURRENCY" => "RUB",
                "ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES" => "Y",
                "ACCOUNT_PAYMENT_SELL_TOTAL" => array("100", "200", "500", "1000", "5000", ""),
                "ACCOUNT_PAYMENT_SELL_USER_INPUT" => "Y",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "ALLOW_INNER" => "N",
                "CACHE_GROUPS" => "Y",
                "CACHE_TIME" => "3600",
                "CACHE_TYPE" => "A",
                "CHECK_RIGHTS_PRIVATE" => "N",
                "COMPATIBLE_LOCATION_MODE_PROFILE" => "N",
                "CUSTOM_PAGES" => "",
                "CUSTOM_SELECT_PROPS" => array(""),
                "MAIN_CHAIN_NAME" => "Мой кабинет",
                "NAV_TEMPLATE" => "",
                "ONLY_INNER_FULL" => "N",
                "ORDERS_PER_PAGE" => "20",
                "ORDER_DEFAULT_SORT" => "STATUS",
                "ORDER_DISALLOW_CANCEL" => "N",
                "ORDER_HIDE_USER_INFO" => array("0"),
                "ORDER_HISTORIC_STATUSES" => array("F"),
                "ORDER_REFRESH_PRICES" => "N",
                "ORDER_RESTRICT_CHANGE_PAYSYSTEM" => array("0"),
                "PATH_TO_BASKET" => "/cart/",
                "PATH_TO_CATALOG" => "/catalog/",
                "PATH_TO_CONTACT" => "/about/contacts/",
                "PATH_TO_PAYMENT" => "/personal/order/payment/",
                "PROFILES_PER_PAGE" => "20",
                "PROP_1" => array(),
                "PROP_2" => array(),
                "SAVE_IN_SESSION" => "Y",
                "SEF_FOLDER" => "/personal/",
                "SEF_MODE" => "Y",
                "SEF_URL_TEMPLATES" => Array(
                    "account" => "account/",
                    "index" => "index.php",
                    "order_cancel" => "cancel/#ID#",
                    "order_detail" => "orders/#ID#",
                    "orders" => "orders/",
                    "private" => "private/",
                    "profile" => "profiles/",
                    "profile_detail" => "profiles/#ID#",
                    "subscribe" => "subscribe/"
                ),
                "SEND_INFO_PRIVATE" => "N",
                "SET_TITLE" => "Y",
                "SHOW_ACCOUNT_COMPONENT" => "Y",
                "SHOW_ACCOUNT_PAGE" => "N",
                "SHOW_ACCOUNT_PAY_COMPONENT" => "Y",
                "SHOW_BASKET_PAGE" => "Y",
                "SHOW_CONTACT_PAGE" => "N",
                "SHOW_ORDER_PAGE" => "Y",
                "SHOW_PRIVATE_PAGE" => "Y",
                "SHOW_PROFILE_PAGE" => "N",
                "SHOW_SUBSCRIBE_PAGE" => "N",
                "USE_AJAX_LOCATIONS_PROFILE" => "N"
            )
        );?>
    </div>
</div>
<?php
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>
