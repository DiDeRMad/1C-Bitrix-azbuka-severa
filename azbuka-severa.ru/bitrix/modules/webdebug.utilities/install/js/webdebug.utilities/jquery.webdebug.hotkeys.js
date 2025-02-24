/**
 *	Simple jQuery-plugin for hot keys!
 *	Copyright 2020-07-22, www.Webdebug.ru, written by Denis Son
 *	-----------------------------------------------------------
 *	Register hotkey:
 *	$.wduHotkey('Ctrl+Shift+Win+C', function(e){
 *		console.log('Pressed hotkey «Ctrl+Shift+Win+C»');
 *	});
 *	$.wduHotkey('Ctrl+Shift+Cmd+C', function(e){
 *		console.log('Pressed hotkey «Ctrl+Shift+Cmd+C»');
 *	});
 *	$.wduHotkey('Ctrl+A', function(e){
 *		console.log('Pressed hotkey «Ctrl+A»');
 *	});
 *	$.wduHotkey('G', function(e){
 *		console.log('Pressed key G');
 *	});
 *	-----------------------------------------------------------
 *	Release hotkey:
 *	$.wduHotkey('Ctrl+Shift+Win+C', false);
 *	$.wduHotkey('Ctrl+Shift+Cmd+C', false);
 *	$.wduHotkey('Ctrl+A', false);
 *	$.wduHotkey('G', false);
 *	-----------------------------------------------------------
 */
$.wduHotkey = function(hotkey, callback) {
	
	// Format hotkey, just for beauty
	function ucFirst(text){
		return text[0].toUpperCase() + text.slice(1).toLowerCase();
	}
	
	// Parse hotkey: 'Ctrl+Alt+A' => {altKey:true, ctrlKey:false, shiftKey:false, metaKey:false, key:A, ...}
	function parseKey(hotkey){
		let
			mod = hotkey.replace(/\s/g, '').split('+'),
			btn = mod.pop(),
			map = {
				'BACKSPACE': 8,
				'TAB': 9,
				'ENTER': 13,
				'ESC': 27,
				'SPACE': 32,
				'PAGE_UP': 33,
				'PAGE_DOWN': 34,
				'END': 35,
				'HOME': 36,
				'INSERT': 45,
				'DELETE': 46,
				'LEFT': 37,
				'UP': 38,
				'RIGHT': 39,
				'DOWN': 40,
				'F1': 112,
				'F2': 113,
				'F3': 114,
				'F4': 115,
				'F5': 116,
				'F6': 117,
				'F7': 118,
				'F8': 119,
				'F9': 120,
				'F10': 121,
				'F11': 122,
				'F12': 123,
			},
			result = {
				key: btn.toUpperCase(),
				keyCode: btn.charCodeAt(0),
				altKey: false,
				ctrlKey: false,
				shiftKey: false,
				metaKey: false,
				hotkey: hotkey
			};
		for(let i=0; i<mod.length; i++){
			switch(ucFirst(mod[i])){
				case 'Alt': result.altKey = true; break;
				case 'Ctrl': result.ctrlKey = true; break;
				case 'Shift': result.shiftKey = true; break;
				case 'Win': result.metaKey = true; break;
				case 'Cmd': result.metaKey = true; break;
			}
		}
		if(map[result.key] != undefined){
			result.key = map[result.key];
		}
		return result;
	}
	
	// Search hotkey and return it index
	function searchHotkey(hotkey){
		let
			result = -1,
			charCode;
		for(let index in $.wduHotkey.list){
			charCode = $.wduHotkey.list[index].key;
			if(typeof $.wduHotkey.list[index].key == 'string'){
				charCode = charCode.charCodeAt(0);
			}
			if(hotkey.keyCode == charCode) {
				if(hotkey.altKey == $.wduHotkey.list[index].altKey){
					if(hotkey.ctrlKey == $.wduHotkey.list[index].ctrlKey){
						if(hotkey.shiftKey == $.wduHotkey.list[index].shiftKey){
							if(hotkey.metaKey == $.wduHotkey.list[index].metaKey){
								result = index;
							}
						}
					}
				}
			}
		}
		return result;
	}
	
	// Register new hotkey
	function registerHotkey(hotkey, callback){
		hotkey.callback = callback;
		$.wduHotkey.list.push(hotkey);
	}
	
	// Release unnecessary hotkey
	function releaseHotkey(hotkey){
		let index = searchHotkey(hotkey);
		if(index > -1){
			delete $.wduHotkey.list[index];
		}
	}
	
	// Initialize
	if(!$.wduHotkey.initialized){
		$.wduHotkey.list = [];
	}
	
	// Create hotkey list
	if(!$.wduHotkey.list){
		$.wduHotkey.list = [];
	}
	
	// Process hotkey
	hotkey = parseKey(hotkey);
	if(typeof callback == 'function'){
		registerHotkey(hotkey, callback);
	}
	else{
		releaseHotkey(hotkey);
	}
	
	// Initialize handler
	if(!$.wduHotkey.initialized){
		$.wduHotkey.initialized = true;
		$(document).keydown(function(e) {
			let index = searchHotkey(e);
			if(index > -1){
				return $.wduHotkey.list[index].callback(e);
			}
		});
	}
	
};
