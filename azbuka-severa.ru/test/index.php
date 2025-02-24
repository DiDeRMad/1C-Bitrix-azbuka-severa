<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");?><?$APPLICATION->IncludeComponent(
	"bitrix:player", 
	".default", 
	array(
		"ADVANCED_MODE_SETTINGS" => "Y",
		"AUTOSTART" => "N",
		"AUTOSTART_ON_SCROLL" => "N",
		"HEIGHT" => "300",
		"MUTE" => "N",
		"PATH" => "",
		"PLAYBACK_RATE" => "1",
		"PLAYER_ID" => "",
		"PLAYER_TYPE" => "auto",
		"PRELOAD" => "Y",
		"REPEAT" => "none",
		"SHOW_CONTROLS" => "Y",
		"SIZE_TYPE" => "absolute",
		"SKIN" => "",
		"SKIN_PATH" => "/bitrix/js/fileman/player/videojs/skins",
		"START_TIME" => "0",
		"VOLUME" => "90",
		"WIDTH" => "400",
		"COMPONENT_TEMPLATE" => ".default",
		"USE_PLAYLIST" => "N",
		"PREVIEW" => "",
		"TYPE" => ""
	),
	false
);?><?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>