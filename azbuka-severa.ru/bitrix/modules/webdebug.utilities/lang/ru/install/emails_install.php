<?
global $MESS;
$strLang = 'WDU_EMAILS_';

$strLangType = $strLang.'WDU_BACKUP_';
$MESS[$strLangType.'NAME'] = 'Создана резервная копия важных данных (webdebug.utilities)';
$MESS[$strLangType.'DESC'] = '
#EMAIL_TO# - Адрес получателя
#FILESIZE# - Размер файла
#FILENAME# - Имя файла
#SITE_ID# - ID сайта
#LANGUAGE_ID# - ID языка
';
$MESS[$strLangType.'DEFAULT_SUBJECT'] = '#SERVER_NAME#: Создана резервная копия важных данных (webdebug.utilities)';
$MESS[$strLangType.'DEFAULT_MESSAGE'] = '
<p>Информационное сообщение сайта #SERVER_NAME#.<br>
---------------------------------------------</p>
<p>Создана резервная копия важных файлов и данных из базы данных.</p>
<p>Размер архива: #FILESIZE#. Файл во вложении.</p>
<p>Имя файла на сайте: <a href="http://#SERVER_NAME#/bitrix/admin/fileman_file_view.php?path=#FILENAME#&site=#SITE_ID#&lang=#LANGUAGE_ID#">#FILENAME#</a>.</p>
<p>---------------------------------------------<br>
Письмо сгенерировано автоматически.</p>
';

$strLangType = $strLang.'WDU_ADMIN_AUTH_';
$MESS[$strLangType.'NAME'] = 'Уведомление об авторизации администратора (webdebug.utilities)';
$MESS[$strLangType.'DESC'] = '
#EMAIL_TO# - Email получатея
#SITE_ID# - ID сайта
#USER_ID# - ID пользователя
#LOGIN# - Логин
#NAME# - Имя и фамилия
#EMAIL# - Email пользователя
#IP# - IP-адрес
#URL# - Адрес страницы
#USER_AGENT# - Браузер
#REFERER# - Источник перехода
#DATETIME# - Источник перехода
#SERVER# - $_SERVER
#TRACE# - Trace
';
$MESS[$strLangType.'DEFAULT_SUBJECT'] = '#SERVER_NAME#: Уведомление об авторизации администратора (webdebug.utilities)';
$MESS[$strLangType.'DEFAULT_MESSAGE'] = '
<p>На сайте #SERVER_NAME# авторизовался администратор.</p>

<ul>
	<li>ID: <a href="http://#SERVER_NAME#/bitrix/admin/user_edit.php?lang=ru&ID=#USER_ID#">#USER_ID#</a></li>
	<li>Логин: <a href="http://#SERVER_NAME#/bitrix/admin/user_edit.php?lang=ru&ID=#USER_ID#">#LOGIN#</a></li>
	<li>Имя и фамилия: #NAME#</li>
	<li>Email: <a href="mailto:#EMAIL#">#EMAIL#<a></li>
	<li>IP-адрес: <a href="https://ip2geolocation.com/?ip=#IP#">#IP#</a></li>
	<li>ID сайта: #SITE_ID#</li>
	<li>URL: <a href="#URL#">#URL#</a></li>
	<li>Браузер: #USER_AGENT#</li>
	<li>Источник: <a href="#REFERER#">#REFERER#</a></li>
	<li>Дата и время: #DATETIME#</li>
</ul>

<p><b><code>$_SERVER</code>:</b></p>
<pre>#SERVER#</pre>

<p><b>Трассировка вызова:</b></p>
<pre>#TRACE#</pre>
';
