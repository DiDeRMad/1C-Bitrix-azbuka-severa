<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('currency');

echo CurrencyFormat(htmlspecialcharsbx($_POST['price']), 'RUB');