<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Вопрос-ответ");
$APPLICATION->SetPageProperty("title", "Вопрос-ответ");
$APPLICATION->SetTitle("Вопрос-ответ");
?><div class="article-content">
	<div class="container">
		<div>
			<h1 class="caption--h1">Вопрос-ответ</h1>
		</div>
		<div class="container-faq">
			 <?$APPLICATION->IncludeComponent("bitrix:support.faq.element.list", "faq_as", Array(
	"AJAX_MODE" => "N",	// Включить режим AJAX
		"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
		"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
		"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
		"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
		"CACHE_GROUPS" => "Y",	// Учитывать права доступа
		"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
		"CACHE_TYPE" => "A",	// Тип кеширования
		"IBLOCK_ID" => "23",	// Список инфоблоков
		"IBLOCK_TYPE" => "main_content",	// Типы инфоблоков
		"PATH_TO_USER" => "",	// Шаблон пути к странице пользователя
		"RATING_TYPE" => "",	// Вид кнопок рейтинга
		"SECTION_ID" => "210",	// ID Секции
		"SHOW_RATING" => "",	// Включить рейтинг
	),
	false
);?>
		</div>
	</div>
</div>
 <br><?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
$APPLICATION->SetTitle("Вопрос-ответ");
?>