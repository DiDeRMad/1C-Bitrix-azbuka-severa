<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Теги");
?>
<?$APPLICATION->IncludeComponent(
	"fouro:tags",
	".default",
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "5",
		"MESSAGE_404" => "",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
