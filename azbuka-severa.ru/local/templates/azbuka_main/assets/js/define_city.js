					var guess_region_div = document.querySelector("#guess_region");

					var choose_city_button = document.getElementById("choose_city_button");

					choose_city_button.onclick = function() {

						var buttons_block = document.querySelector("#guess_region .buttons");
						buttons_block.style.display = "none";

						var show_city_div = document.querySelector("div.show_city");
						show_city_div.style.display = "none";

						var search_input = document.querySelector("#guess_region .search_input");
						search_input.style.display = "block";

						guess_region_div.className = "choose_city";
					};

					/* стрелка назад */

					var back_arrow = document.querySelector(".back_arrow");

					back_arrow.onclick = function() {

						var buttons_block = document.querySelector("#guess_region .buttons");
						buttons_block.style.display = "block";

						var show_city_div = document.querySelector("div.show_city");
						show_city_div.style.display = "block";

						var search_input = document.querySelector("#guess_region .search_input");
						search_input.style.display = "none";

						guess_region_div.className = "";
					};

					/* выбор города */

					var timer_interval = setInterval(function() {
						
						console.log(10);

						var click_buttons = document.querySelectorAll(".click_button");

						if (click_buttons.length != 0) {
							
							if ( ( document.location.href.indexOf("azbuka-severa.ru") != (-1) ) && ( document.cookie.indexOf("close_modal_window") == (-1) ) ) {

								// guess_region_div.style.display = "block";
								
								guess_region_div.setAttribute("data-need_open", 1);

								document.cookie = "close_modal_window=1; max-age=86400";
							}

							if (localStorage.getItem("chosen_real_city")) {

								/* город уже выбран */

								var chosen_city = localStorage.getItem("chosen_real_city");

								var city_a_element = document.querySelector("#city_a_element");

								city_a_element.textContent = chosen_city;

								/* guess_region_div.style.display = "none"; */
							}

							for (var i = 0; i < click_buttons.length; i++) {

								var cur_button = click_buttons[i];

								cur_button.onclick = function() {

									var chosen_button = $(this)[0];

									var chosen_city = chosen_button.textContent;

									if (chosen_city == "Санкт-Петербург") {

										localStorage.setItem("chosen_city", "Spb");
										localStorage.setItem("chosen_real_city", "Санкт-Петербург");
									} else {

										if (chosen_city == "Да") {

											chosen_city = "Москва";
										}

										localStorage.setItem("chosen_city", "Moscow");
										localStorage.setItem("chosen_real_city", chosen_city);
									}

									var city_a_element = document.querySelector("#city_a_element");

									city_a_element.textContent = chosen_city;

									guess_region_div.style.display = "none";
								};

							}

							clearInterval(timer_interval);

							/* выбор города по клику */

							var city_a_element = document.querySelector("#city_a_element");

							city_a_element.onclick = function() {

								guess_region_div.style.display = "block";
							};

						}

					}, 300);
					
					/* определение текущего города */
					
					function set_city_value ( result ) {
						
					console.log( JSON.parse(result) );	
						
					$("#real_city_value")[0].textContent = JSON.parse(result)['location']['data']['city'];	
					
					if ( guess_region_div.getAttribute("data-need_open") ) {
						
					guess_region_div.removeAttribute("data-need_open");

					guess_region_div.style.display = "block";
					}
					
					}
					
					function get_cur_city (ip_address) {
						
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip=";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var options = {
					method: "GET",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					}
					}
					
					fetch(url + ip_address, options)
					.then(response => response.text())
					.then(result => set_city_value (result) )
					.catch(error => console.log("error", error));	
					}
					
					fetch('https://ipapi.co/json/')
					.then(d => d.json())
					.then(d =>  get_cur_city (d.ip) );
					
					/* установка масок на номера */
					
					setTimeout( function () {
						
					var tel_input_main_page_center = document.querySelector("input#koptil-phone");

                    if (tel_input_main_page_center) {
						
					$(tel_input_main_page_center).mask("+7(999) 999-99-99");	
					}		

                    var call_input_bottom = document.querySelector("#modal-callback #callback_form input[type='tel']");					
					
                    if (call_input_bottom) {
						
					$(call_input_bottom).mask("+7(999) 999-99-99");	
					}		

                 	var send_message_bottom = document.querySelector("#send-message #message_form input[type='tel']");					
					
                    if (send_message_bottom) {
						
					$(send_message_bottom).mask("+7(999) 999-99-99");	
					}	

                    var pod_zakaz_input = document.querySelector("#modal-item-buy #item-buy_form input[type='tel']");	

                    if (pod_zakaz_input) {
						
					$(pod_zakaz_input).mask("+7(999) 999-99-99");	
					}
						
					}, 3000);
					
					setTimeout( function () {
						
					/* меняющийся плэйсхолдер */
					
					var input_placeholder = document.querySelector("input#title-search-input");
					
					var letters = ["И", "с", "к", "а", "т", "ь", " ", "т", "о", "в", "а", "р", "ы"];
					
					var placeholder_text = "И";
					var index = -1;
					
					setInterval( function () {
						
					index++;
					
					if (index > 12) {
						
					index = 0;	
					}
					
					var word_str = "";
					
					for (var i = 0; i <= index; i++) {
						
					word_str += letters[i];	
					}
					
					input_placeholder.setAttribute("placeholder", word_str);
						
					}, 300);					
						
					}, 2000);	
					
					
					/* фокус на поле - искать товары */
					
					function getStyle (elem) {
    
	                return window.getComputedStyle ? getComputedStyle (elem, "") : elem.currentStyle;
                    }
					
					var lupa_element = document.querySelector("form.header-search__form button.header-search__btn");
					var lupa_element_mobile = document.querySelector("div.header-search button.header-search-mobile__btn");
					var input_text_field = document.querySelector("input#title-search-input");
					var header_form = document.querySelector("form.header-search__form");
					
					var cur_style_param = getStyle(lupa_element_mobile).display;
					
					if ( (lupa_element_mobile) && ( cur_style_param != "none" ) ) {
						
					input_text_field.setAttribute("autofocus", "true");	
						
					/* мобильная версия */	
					
                    setInterval( function () {
						
					var is_form_opened = getStyle( header_form ).display;

					if ( ( is_form_opened != "none" ) && ( !header_form.hasAttribute("data-show") ) ) {
						
					header_form.setAttribute("data-show", 1);	
					
					$(input_text_field).mouseup( function(e) {
                    
					e.preventDefault();
					});
						
					input_text_field.focus();	
					input_text_field.click();	
					}
					
					if ( ( is_form_opened == "none" ) && ( header_form.hasAttribute("data-show") ) ) {
						
					header_form.removeAttribute("data-show");	
					}
						
					}, 200);					
					
					}
					
					lupa_element.onclick = function () {
						
					var is_input_text_field_focused = 0;

                    var text_input_field_element_class = document.querySelector(".header-bottom__wrapper div.header-search").className;

                 	if ( text_input_field_element_class.indexOf("active") == (-1) ) {
						
					input_text_field.focus();	
					
					return false;	
					} else {
						
					header_form.submit();	
					
					return false;
					}	
					
					};
					
					/* lupa_element_mobile.addEventListener("touchstart", function () {
						
					var is_input_text_field_focused = 0;

                    var text_input_field_element_class = document.querySelector(".header-bottom__wrapper div.header-search").className;

                 	if ( text_input_field_element_class.indexOf("active") == (-1) ) {
						
					setTimeout( function () {
						
					input_text_field.focus();		
						
					}, 600);	
					
					return false;	
					} else {
						
					header_form.submit();

                    return false;					
					}	
					
					});
					
					lupa_element_mobile.addEventListener("touchend", function () {
						
					var is_input_text_field_focused = 0;

                    var text_input_field_element_class = document.querySelector(".header-bottom__wrapper div.header-search").className;

                 	if ( text_input_field_element_class.indexOf("active") == (-1) ) {
						
					setTimeout( function () {
						
					input_text_field.focus();		
						
					}, 600);	
					
					return false;	
					} else {
						
					header_form.submit();	
					
					return false;
					}	
					
					});
					
					lupa_element_mobile.addEventListener("click", function () {
						
					var is_input_text_field_focused = 0;

                    var text_input_field_element_class = document.querySelector(".header-bottom__wrapper div.header-search").className;

                 	if ( text_input_field_element_class.indexOf("active") == (-1) ) {
						
					setTimeout( function () {
						
					input_text_field.focus();		
						
					}, 600);
					
					return false;	
					} else {
						
					header_form.submit();	
					
					return false;
					}	
					
					}); */
					
					input_text_field.onkeypress = function (e) {
						
					if (e.keyCode === 13) {
					
					header_form.submit();
					}
					
					};