<?
list($strLang, $strName, $strHint) = \WD\Utilities\Options::getLang();

$MESS[$strLang.'GROUP_GENERAL_INTERFACE'] = 'Настройки интерфейса';
#
$MESS[$strName.'USE_SELECT2_FOR_MODULES'] = 'Использовать плагин jQuery select2 для списка модулей';
	$MESS[$strHint.'USE_SELECT2_FOR_MODULES'] = 'Опция позволяет для выпадающего списка модулей использовать jQuery-плагин select. Это дает возможность поиска (фильтра) по выпадающему списку.';
#
$MESS[$strName.'USE_SELECT2_FOR_CUSTOM_ADMIN_PAGES'] = 'Использовать плагин jQuery select2 для произвольных страниц';
	$MESS[$strHint.'USE_SELECT2_FOR_CUSTOM_ADMIN_PAGES'] = 'Опция позволяет использовать jQuery-плагин select для произвольных страниц административного раздела. Для публичной части сайта это не будет работать!';
$MESS[$strName.'CUSTOM_PAGES_FOR_SELECT2'] = 'jQuery select2 для произвольных страниц';
	$MESS[$strHint.'CUSTOM_PAGES_FOR_SELECT2'] = 'Укажите, на каких страницах и для каких селекторов будет использоваться jQuery select2. Для каждой страницы можно указать несколько селекторов - каждый на отдельной строке.<br/><br/>
		Селектор будет помещён в JS-строку с косыми кавычками, поэтому в качестве кавычек селекторы могут использовать как одинарные, так и двойные кавычки.<br/><br/>
		Пример адреса:<br/>
		<code><b>/bitrix/admin/user_edit.php</b></code><br/><br/>
		Пример селектора:<br/>
		<code><b>select[name="LID"]<br/>
		#bx_EXTERNAL_AUTH_ID<br/>
		select[name="PERSONAL_COUNTRY"]</code><br/>';
	$MESS[$strLang.'SELECT2_URL_PLACEHOLDER'] = 'Адрес страницы (без параметров)';
	$MESS[$strLang.'SELECT2_SELECTOR_PLACEHOLDER'] = 'jQuery-селектор';
	$MESS[$strLang.'SELECT2_BUTTON_ADD'] = 'Добавить';
	$MESS[$strLang.'SELECT2_BUTTON_DELETE'] = 'Удалить';
#
$MESS[$strName.'SET_ADMIN_FAVICON'] = 'Устанавливать в админке собственную favicon';
	$MESS[$strHint.'SET_ADMIN_FAVICON'] = 'Опция позволяет установить для админинстративного раздела собственную иконку сайта. Может быть полезно когда на сайте favicon.ico находится не в корне сайта. Или для более удобного ориентирования в большом количестве вкладок браузера.';
#
$MESS[$strName.'ADMIN_FAVICON'] = 'Административная favicon';
	$MESS[$strHint.'ADMIN_FAVICON'] = 'Здесь необходимо указать путь к файлу favicon.ico относительно корня сайта.';
#
$MESS[$strName.'HIDE_PARTNERS_MENU'] = 'Скрыть (перенести) лишние пункты меню';
	$MESS[$strHint.'HIDE_PARTNERS_MENU'] = 'Опция позволяет перенести пункты административного меню, добавляемые сторонними модулями, в отдельное подменю:<br/><b>«Настройки»</b> - <b>«Дополнительное меню»</b> (в самом низу).<br/><br/>При этом вы сами управляете тем, какие пункты оставить.';
#
$MESS[$strName.'HIDE_PARTNERS_MENU_EXCLUDE'] = 'Пункты главного меню, которые будут показаны';
	$MESS[$strHint.'HIDE_PARTNERS_MENU_EXCLUDE'] = 'Укажите здесь (через запятую) коды пунктов меню, которые не нужно переносить. Значение по умолчанию:<br/><code>desktop, content, landing, marketing, store, services, analytics, marketPlace, settings</code><br/><br/>
		<b>Обратите внмиание!</b> Группы меню будут выводиться в таком порядке, в котором Вы их здесь укажите - таким образом Вы можете отсортировать меню.<br/></br>
		Меню «Настройки» невозможно скрыть.<br/><br/>
		Если поле не заполнено, показываются все пункты меню.';
$MESS[$strLang.'HIDE_PARTNERS_MENU_EXCLUDE_POPUP_TITLE'] = 'Выберите, какие меню будут доступны';
$MESS[$strLang.'HIDE_PARTNERS_MENU_EXCLUDE_LOADING'] = 'Загрузка..';
$MESS[$strLang.'HIDE_PARTNERS_MENU_EXCLUDE_SAVE'] = 'Сохранить';
#
$MESS[$strName.'POPUP_EXPAND_ON_TOP_EDGE'] = 'Автоматически разворачивать окна у верхнего края сайта';
	$MESS[$strHint.'POPUP_EXPAND_ON_TOP_EDGE'] = 'Опция позволяет автоматически разворачивать стандартные popup-окна (BX.CWindow) при поднесении их к верхнему краю сайта.<br/><br/>Работает только для администраторов.';
#
$MESS[$strName.'INCLUDE_ACCESSIBILITY_JS'] = 'Подключать средства доступности веб-форм';
	$MESS[$strHint.'INCLUDE_ACCESSIBILITY_JS'] = '<b>Экспериментальная функция!</b><br/><br/>
	Опция подключает скрипт, который добавляет несколько улучшения по работе с формами в административном разделе. В частности, добавлена поддержка обычной навигации с клавиатуры: напр., нажатие Tab теперь может поставить фокус на галочку, и чтобы её отметить, останется только нажать Enter или пробел. Аналогично и с формами в новых UI-списках (дополнительно: кнопка становится по умолчанию выбранной).<br/><br/>
	Включается только для текущего пользователя.';

?>