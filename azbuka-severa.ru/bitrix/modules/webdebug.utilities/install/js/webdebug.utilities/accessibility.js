// Initialize
window.wdu_bx_accessibility_init = function(){
	// Inject CSS
	if(!document.getElementById('wdu_bx_accessibility')){
		let
			style = document.createElement('style'),
			cssCode = `
				/* Checkboxes */
				#bx-admin-prefix .adm-designed-checkbox + .adm-designed-checkbox-label:focus {
					outline:none;
					box-shadow:0 0 2px 2px rgb(0 255 0);
					width:15px;
					height:15px;
					border-radius:3px;
				}
				/* Buttons in UI popup */
				.popup-window-buttons > span.popup-window-button:focus {
					box-shadow:0 0 2px 2px rgb(0 255 0);
				}
				/* Checkboxes in admin list */
				table.adm-list-table .adm-list-table-cell.adm-list-table-checkbox .adm-designed-checkbox-label {
					width:18px!important;
				}
			`;
		style.id = 'wdu_bx_accessibility';
		style.type = 'text/css';
		if(style.styleSheet) { // IE
			style.styleSheet.cssText = cssCode;
		}
		else { // Other browsers
			style.innerHTML = cssCode;
		}
		document.getElementsByTagName('head')[0].appendChild(style);
	}
	// Handlers
	if(typeof BX == 'function' && typeof BX.addCustomEvent == 'function'){
		let events = ['BX.Main.Filter:blur', 'BX.Main.Filter'];
		for(let i in events){
			if(events.hasOwnProperty(i)){
				BX.addCustomEvent(window, events[i], function(e){
					setTimeout(function(){
						window.wdu_bx_accessibility_apply();
					}, 100);
				});
			}
		}
	}
	// Initial apply
	window.wdu_bx_accessibility_apply();
}

// Process items
window.wdu_bx_accessibility_apply = function(){
	// Process all items
	let items = document.querySelectorAll('.adm-designed-checkbox-label, .popup-window-buttons > span.popup-window-button');
	for(let i in items){
		if(items.hasOwnProperty(i) && !items[i].wdu_bx_accessibility_handled){
			items[i].setAttribute('tabindex', '0');
			items[i].addEventListener('keydown', function(e) {
				if([13, 32].indexOf(e.keyCode) > -1){ // 13 - Enter, 32 - Space
					e.preventDefault();
					this.click();
				}
			});
			this.wdu_bx_accessibility_handled = true;
		}
	}
	// Focus elements
	window.wdu_bx_accessibility_focus();
}

// Focus item
window.wdu_bx_accessibility_focus = function(){
	setTimeout(function(){
		let items = document.querySelectorAll('.popup-window-buttons > span.popup-window-button');
		if(items.length){
			items[0].focus();
		}
	}, 50)
}

window.wdu_bx_accessibility_init();
document.addEventListener('DOMContentLoaded', function(){
	window.wdu_bx_accessibility_init();
});
