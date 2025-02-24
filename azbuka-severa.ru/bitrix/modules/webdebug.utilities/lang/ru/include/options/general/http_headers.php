<?
list($strLang, $strName, $strHint) = \WD\Utilities\Options::getLang();

$MESS[$strLang.'GROUP_GENERAL_HTTP_HEADERS'] = 'Управление заголовками ответа сервера';
#
$MESS[$strName.'SERVER_HEADERS_REMOVE'] = 'Удалить заголовки';
	$MESS[$strHint.'SERVER_HEADERS_REMOVE'] = 'Данная опция позволяет удалить некоторые заголовки из ответа страницы.<br/><br/>
	Имейте в виду, что могут быть удалены только те заголовки, которые были выставлены PHP и (или) Битриксом. Заголовки, устанавливаемые веб-сервером, или nginx, удалены не могут быть, например: Server, Date, Connection и др.<br/><br/>
	Однако некоторые из них могут быть переопределены: Content-Type, Accept-Language, Accept-Encoding, Cache-Control и др.<br/><br/>
	При возникновении проблем читайте <a href="https://www.webdebug.ru/marketplace/webdebug.utilities/?tab=faq#36175" target="_blank">здесь</a> или пишите нам в <a href="https://www.webdebug.ru/marketplace/webdebug.utilities/?tab=ask#ask_marketplace" target="_blank">техподдержку</a>.';
	$MESS[$strLang.'SERVER_HEADERS_REMOVE_PLACEHOLDER'] = 'Например, Expires';
#
$MESS[$strName.'SERVER_HEADERS_ADD'] = 'Добавить заголовки';
	$MESS[$strHint.'SERVER_HEADERS_ADD'] = 'Данная опция позволяет добавить произвольные заголовки ответа страницы.<br/><br/>
	Используется стандартная PHP-функция header().<br/><br/>Имейте ввиду, что стандартные заголовки (напр., Server, Date, Connection и др.) не могут быть переопределены.<br/><br/>
	При возникновении проблем читайте <a href="https://www.webdebug.ru/marketplace/webdebug.utilities/?tab=faq#36175" target="_blank">здесь</a> или пишите нам в <a href="https://www.webdebug.ru/marketplace/webdebug.utilities/?tab=ask#ask_marketplace" target="_blank">техподдержку</a>.<br/><br/>
	Примеры установки заголовков:
	<ul style="font-family:monospace;">
		<li>X-Powered-By: PHP/8.0.0</li>
		<li>Cache-Control: max-age=30, public</li>
		<li>Content-Type: text/html; charset=UTF-8</li>
		<li>Server: MyHttpServer/1.0.1</li>
		<li>X-Powered-CMS: Joomla! 3.8.12</li>
	</ul>';
	$MESS[$strLang.'SERVER_HEADERS_ADD_PLACEHOLDER'] = 'Например, X-Powered-By: PHP/8.0.0';
#
$MESS[$strLang.'SERVER_HEADERS_BUTTON_ADD'] = 'Добавить';
$MESS[$strLang.'SERVER_HEADERS_BUTTON_DELETE'] = 'Удалить';

?>