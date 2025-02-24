// Add checking Array.isArray (check if object is array or not)
if(typeof Array.isArray === 'undefined') {
  Array.isArray = function(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
  }
};

// Format strings
if(!String.wduFormat) {
	String.wduFormat = function(format) {
		var args = Array.prototype.slice.call(arguments, 1);
		return format.replace(/{(\d+)}/g, function(match, number) { 
			return typeof args[number] != 'undefined' ? args[number] : match
			;
		});
	};
}

// Serialize to JSON: https://css-tricks.com/snippets/jquery/serialize-form-to-json/
if($.fn && !$.fn.serializeObject){
	$.fn.serializeObject = function() {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function() {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Build http query
function wduHttpBuildQuery(params) {
	let
		query = [];
	for(let param in params){
		if(typeof params[param] == 'object' && Array.isArray(params[param])){
			for(let index in params[param]){
				query.push(String.wduFormat('{0}[{1}]={2}', encodeURIComponent(param), encodeURIComponent(index), 
					encodeURIComponent(params[param][index])));
			}
		}
		else{
			query.push(String.wduFormat('{0}={1}', encodeURIComponent(param), encodeURIComponent(params[param])));
		}
	}
	return query.join('&');
}

// Ajax
function wduAjax(option, get, post, callbackSuccess, callbackError, hideLoader) {
	get = get != null && typeof get == 'object' ? get : {};
	post = post != null && typeof get == 'object' ? post : {};
	get.wdu_ajax_option = option;
	if(hideLoader !== true) {
		BX.showWait();
	}
	return BX.ajax({
		url: wduGetCurPageParam(wduHttpBuildQuery(get), []),
		method: 'POST',
		data: post,
		dataType: 'json',
		timeout: 30,
		async: true,
		processData: true,
		scriptsRunFirst: false,
		emulateOnload: false,
		start: true,
		cache: false,
		onsuccess: function(arJsonResult){
			if(typeof callbackSuccess == 'function') {
				callbackSuccess(arJsonResult);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		},
		onfailure: function(status, error){
			console.error(status, error);
			if(typeof callbackError == 'function') {
				callbackError(error);
			}
			if(hideLoader!==true) {
				BX.closeWait();
			}
		}
	});
}

// Get cur page param [analog for $APPLICATION->getCurPageParam()]
function wduGetCurPageParam(strAdd, arRemove, bAtTheEnd){
	var arData = [];
		arDataTmp = [],
		arGetParts = location.search.substr(1).split('&'),
		strQuery = '';
	strAdd = typeof strAdd == 'string' ? strAdd : strAdd.toString();
	arRemove = typeof arRemove == 'object' ? arRemove : [arRemove];
	bAtTheEnd = bAtTheEnd === true ? true : false;
	for(var i in arGetParts){
		if(arGetParts[i].length){
			var item = arGetParts[i].split('=');
			arDataTmp.push({
				name: item[0],
				value: decodeURIComponent(item[1])
			});
		}
	}
	for(var i in arDataTmp){
		var strName = arDataTmp[i].name.split('[')[0],
			bDelete = false;
		for(var j in arRemove){
			if(arRemove[j] == strName){
				bDelete = true;
				break;
			}
		}
		if(!bDelete){
			arData.push(arDataTmp[i]);
		}
	}
	for(var i in arData){
		strQuery += '&' + arData[i].name + '=' + encodeURIComponent(arData[i].value);
	}
	strQuery = strQuery.substr(1);
	if(bAtTheEnd){
		strQuery = (strQuery.length ? strQuery + '&' : '') + strAdd;
	}
	else{
		strQuery = strAdd + (strQuery.length ? '&' + strQuery : '');
	}
	if(strQuery.substr(0, 1) == '&'){
		strQuery = strQuery.substr(1);
	}
	if(strQuery.length){
		strQuery = '?' + strQuery;
	}
	if(strQuery.substr(-1) == '&'){
		strQuery = strQuery.slice(0, -1);
	}
	return location.href.split('?')[0] + strQuery;
}

// Change param in url
function wduChangeUrl(key, value){
	if(document.readyState == 'complete') {
		value = (typeof value == 'number' && value > 0 || typeof value == 'string' && value.length 
			? key+'='+encodeURIComponent(value) : '');
		var newUrl = wduGetCurPageParam(value, [key], true);
		wduSetUrl(newUrl);
	}
}

// Set url
function wduSetUrl(url){
	window.history.pushState('', '', url);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* Error popup */
let wduPopupError;
wduPopupError = new WduPopup({
	height: 200,
	width: 600
});
wduPopupError.Open = function(title, error){
	let popup = this;
	popup.WdSetTitle(title);
	popup.WdSetContent(error);
	popup.WdSetNavButtons([{
		'name': BX.message('JS_CORE_WINDOW_CLOSE'),
		'id': 'wdu_finder_error_close',
		'className': 'adm-btn-green',
		'action': function(){
			popup.Close();
		}
	}]);
	popup.Show();
}


