<?
list($strLang, $strName, $strHint) = \WD\Utilities\Options::getLang();

$MESS[$strLang.'GROUP_GENERAL_ADMIN_AUTH'] = 'Уведомления об авторизации администраторов';
#
$MESS[$strName.'ADMIN_AUTH_NOTIFY'] = 'Уведомления включены';
	$MESS[$strHint.'ADMIN_AUTH_NOTIFY'] = 'Опция позволяет получать уведомления об авторизации администраторов на сайте.';
$MESS[$strName.'ADMIN_AUTH_EMAIL'] = 'E-mail для уведомления';
	$MESS[$strHint.'ADMIN_AUTH_EMAIL'] = 'Укажите здесь один или несколько email-адресов (разделенных запятой) для отправки уведомлений.';
$MESS[$strName.'ADMIN_AUTH_HTTP_REQUEST'] = 'Выполнить HTTP-запрос';
	$MESS[$strHint.'ADMIN_AUTH_HTTP_REQUEST'] = 'Укажите здесь адрес http-запроса, используя следующие макросы:
	<ul>
		<li><code>#SUBJECT#</code> - тема сообщения,</li>
		<li><code>#MESSAGE#</code> - текст сообщения,</li>
		<li><code>#USER_ID#</code> - ID пользователя</li>
		<li><code>#USER_LOGIN#</code> - логин пользователя</li>
		<li><code>#USER_EMAIL#</code> - email пользователя</li>
		<li><code>#USER_NAME#</code> - имя и фамилия пользователя</li>
		<li><code>#USER_IP#</code> - IP-адрес</li>
		<li><code>#SITE_ID#</code> - ID сайта</li>
	</ul>
<p>Примеры сервисов для отправки уведомлений:</p>
<ul>
	<li><a href="https://webdebug.sms.ru/api/send">sms.ru</a> - отправка SMS на телефон,</li>
	<li><a href="https://semysms.net/api.php#sendsms">semysms.net</a> - отправка SMS со своего номера (Android),</li>
	<li><a href="https://wirepusher.com">wirepusher.com</a> - push-уведомления на телефон (Android),</li>
	<li><a href="https://pushall.ru/blog/api">pushall.ru</a> - push-уведомления на телефон (Android),</li>
</ul>';

?>