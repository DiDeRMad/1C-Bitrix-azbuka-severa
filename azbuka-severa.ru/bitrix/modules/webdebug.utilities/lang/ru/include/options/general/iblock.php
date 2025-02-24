<?
list($strLang, $strName, $strHint) = \WD\Utilities\Options::getLang();

$MESS[$strLang.'GROUP_GENERAL_IBLOCK'] = 'Полезности для инфоблоков';
#
$MESS[$strName.'IBLOCK_ADD_DETAIL_LINK'] = 'Кнопка просмотра товара в форме редактирования';
	$MESS[$strHint.'IBLOCK_ADD_DETAIL_LINK'] = 'Опция добавляет на страницу редактирования товара кнопку «Просмотр на сайте» (в зависимости от текущей настройки, либо в меню «Действия», либо непосредственно на панель кнопок), которая ведет на страницу сайта с карточкой товара.<br/><br/>
	Домен определяется на основе сайта, к которому привязан инфоблок.';
	$MESS[$strLang.'IBLOCK_ADD_DETAIL_LINK_NO'] = 'Не выводить';
	$MESS[$strLang.'IBLOCK_ADD_DETAIL_LINK_SUBMENU'] = 'В подменю';
	$MESS[$strLang.'IBLOCK_ADD_DETAIL_LINK_SEPARATE'] = 'Отдельно';
#
$MESS[$strName.'IBLOCK_SHOW_ELEMENT_ID'] = 'Показывать ID элемента в форме редактирования';
	$MESS[$strHint.'IBLOCK_SHOW_ELEMENT_ID'] = 'Опция позволяет вывести ID товара в форме редактирования (на панель с кнопками «Сохранить», «Применить», «Отменить»).<br/><br/>
		Работает в т.ч. в popup-окне редактирования товара.';
#
$MESS[$strName.'IBLOCK_JUST_THIS_SITE'] = 'Показывать в меню инфоблоки только текущего сайта';
	$MESS[$strHint.'IBLOCK_JUST_THIS_SITE'] = 'При включенной опции модуль определяет текущий сайт по домену, и скрывает из меню все инфоблоки, не привязанные к текущему сайту.';
#
$MESS[$strName.'IBLOCK_HIDE_EMPTY_TYPES'] = 'Не показывать в меню пустые типы инфоблоков';
	$MESS[$strHint.'IBLOCK_HIDE_EMPTY_TYPES'] = 'Опция удаляет из меню все пустые типы инфоблоков (т.е. такие, в которых нет ни одного инфоблока).';
#
$MESS[$strName.'IBLOCK_SHOW_IBLOCK_PROP_META'] = 'Показывать ID и коды свойств в формах редактирования';
	$MESS[$strHint.'IBLOCK_SHOW_IBLOCK_PROP_META'] = 'Опция позволяет в формах редактирования товара выводить под каждым свойством его ID и символьный код. Например, если в форме редактирования товара выводится свойство<br/><br/>
	Артикул:<br/><br/>
	то при включении данной опции будет примерно так:<br><br/>
	Артикул:<br/>
	<small>123, CML2_ARTICLE</small>';
#
$MESS[$strName.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES'] = 'Скрывать вкладку «Свойства элементов» [для разделов]';
	$MESS[$strHint.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES'] = 'Опция скрывает вкладку с настройкой свойств элемента для конкретного раздела. Позволяет немного ускорить загрузку страницы при большом количестве свойств.<br/><br/>Опция применяется только для текущего пользователя.';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES_NO'] = '-- не скрывать --';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES_PUBLIC'] = 'скрывать только в публичной части';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES_ADMIN'] = 'скрывать только в административном разделе';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_PROPERTIES_YES'] = 'скрывать всегда';
#
$MESS[$strName.'IBLOCK_HIDE_SECTION_TAB_SEO'] = 'Скрывать вкладку «SEO» [для разделов]';
	$MESS[$strHint.'IBLOCK_HIDE_SECTION_TAB_SEO'] = 'Опция скрывает вкладку с настройкой свойств элемента для конкретного раздела. Позволяет немного ускорить загрузку страницы при большом количестве свойств (т.к. все свойства используются в выпадающих меню для настройки макросов).<br/><br/>Опция применяется только для текущего пользователя.';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_SEO_NO'] = '-- не скрывать --';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_SEO_PUBLIC'] = 'скрывать только в публичной части';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_SEO_ADMIN'] = 'скрывать только в административном разделе';
	$MESS[$strLang.'IBLOCK_HIDE_SECTION_TAB_SEO_YES'] = 'скрывать всегда';
#
$MESS[$strName.'IBLOCK_HIDE_ELEMENT_TAB_SEO'] = 'Скрывать вкладку «SEO» [для элементов]';
	$MESS[$strHint.'IBLOCK_HIDE_ELEMENT_TAB_SEO'] = 'Опция скрывает вкладку с настройкой свойств элемента для конкретного раздела. Позволяет немного ускорить загрузку страницы при большом количестве свойств (т.к. все свойства используются в выпадающих меню для настройки макросов).<br/><br/>Опция применяется только для текущего пользователя.';
	$MESS[$strLang.'IBLOCK_HIDE_ELEMENT_TAB_SEO_NO'] = '-- не скрывать --';
	$MESS[$strLang.'IBLOCK_HIDE_ELEMENT_TAB_SEO_PUBLIC'] = 'скрывать только в публичной части';
	$MESS[$strLang.'IBLOCK_HIDE_ELEMENT_TAB_SEO_ADMIN'] = 'скрывать только в административном разделе';
	$MESS[$strLang.'IBLOCK_HIDE_ELEMENT_TAB_SEO_YES'] = 'скрывать всегда';
#
$MESS[$strName.'IBLOCK_HIDE_ELEMENT_TAB_ADV'] = 'Скрыть вкладку «Реклама» [для элементов]';
	$MESS[$strHint.'IBLOCK_HIDE_ELEMENT_TAB_ADV'] = 'Опция скрывает вкладку «Реклама» на странице редактирования товара.';
$MESS[$strName.'IBLOCK_HIDE_ELEMENT_TAB_WF'] = 'Скрыть вкладку «Документооборот» [для элементов]';
	$MESS[$strHint.'IBLOCK_HIDE_ELEMENT_TAB_WF'] = 'Опция скрывает вкладку «Документооборот» на странице редактирования товара.';
$MESS[$strName.'IBLOCK_HIDE_ELEMENT_TAB_BP'] = 'Скрыть вкладку «Бизнес-процессы» [для элементов]';
	$MESS[$strHint.'IBLOCK_HIDE_ELEMENT_TAB_BP'] = 'Опция скрывает вкладку «Бизнес-процессы» на странице редактирования товара.';


?>