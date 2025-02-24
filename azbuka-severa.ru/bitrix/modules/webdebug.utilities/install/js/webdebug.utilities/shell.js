let
	wduTerminal;

$(document).on('click', 'a[data-role="wdu_shell_toggle_fullscreen"]', function(e){
	e.preventDefault();
	$('[data-role="wdu_terminal"]').toggleFullScreen();
});

$.wduHotkey('Alt+Enter', function(e){
	$('[data-role="wdu_terminal"]').toggleFullScreen();
});

$(document).ready(function(){
	wduTerminal = $('div[data-role="wdu_terminal"]');
	wduTerminal.terminal(wduGetCurPageParam('wdu_ajax_option=shell_execute', ['wdu_ajax_option']), {
		name: 'wdu_shell',
		greetings: wduTerminal.attr('data-greetings'),
		width: '100%',
		height: 500,
		prompt: '> '
	});
});
