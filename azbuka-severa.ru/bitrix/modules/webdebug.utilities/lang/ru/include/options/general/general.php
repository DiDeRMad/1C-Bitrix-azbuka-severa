<?
list($strLang, $strName, $strHint) = \WD\Utilities\Options::getLang();

$MESS[$strLang.'GROUP_GENERAL_GENERAL'] = 'Общие настройки';
#
$MESS[$strName.'PAGEPROPS_ENABLED'] = 'Включить удобные свойства страниц/разделов';
	$MESS[$strHint.'PAGEPROPS_ENABLED'] = 'Опция включает функционал удобных свойств.<br/><br/>
	Удобные свойства настраиваются в настройках модуля «Управления структурой» (в поле «Типы свойств» создается колонка с кнопкой, где для каждого свойства можно определить параметры), и действует при настройке этих свойств для страниц и разделов - например, если на любой странице сайта нажать «Изменить страницу» (подменю) - «Заголовок и свойства страницы».';
#
$MESS[$strName.'PREVENT_LOGOUT'] = 'Подтверждение выхода (кнопка «Выйти» на панели)';
	$MESS[$strHint.'PREVENT_LOGOUT'] = 'Данная опция может быть весьма полезной для тех, кто случайно нажимает «Выйти» на административной панели.<br/><br/>
	<b>Внимание!</b> Опция работает отдельно для каждого администратора.';
#
$MESS[$strName.'HIDE_ADVERTISING'] = 'Не показывать рекламный баннер';
	$MESS[$strHint.'HIDE_ADVERTISING'] = 'Отметьте опцию, если Вы не хотите видеть рекламный баннер.<br/><br/>
	Данная опция включается/отключается отдельно для каждого пользователя.';

?>