<?
$strLang = 'WDU_SHELL_';
$strHint = $strLang.'HINT_';

$MESS[$strLang.'PAGE_TITLE'] = 'Командная строка Shell';

$MESS[$strLang.'NO_EXEC'] = 'Команда <code>exec()</code> недоступна на Вашем сайте. Работа командной строки невозможна.';
$MESS[$strLang.'NO_EXEC_DETAILS'] = 'Командная строка работает через функцию <code>exec()</code>, но эта функция недоступна.<br/>
Проверьте конфигурационный PHP-параметр disable_functions - если в нём указана команда <code>exec</code>, значит данная функция заблокирована в целях безопасности.';

$MESS[$strLang.'FULLSCREEN'] = 'Для перехода в полноэкранный режим нажмите <a href="#" class="wdu_inline_link" data-role="wdu_shell_toggle_fullscreen"><code><b>Alt+Enter</b></code></a>.';

$MESS[$strLang.'NOTES'] = 
'<b>Внимание!</b> Данная консоль не является полноценной SSH-консолью, а всего лишь выполняет команды с помощью PHP-функции <code>exec()</code>.<br/><br/>
<b>Пожалуйста, будьте внимательны и осторожны.</b> Мы не несём ответственности за любые последствия, связанные с использованием данного инструмента.<br/><br/>
Визуальная часть консоли - jQuery-плагин <a href="https://terminal.jcubic.pl/" target="_blank">Terminal</a> (разработчик - Jakub Jankiewicz).';

?>