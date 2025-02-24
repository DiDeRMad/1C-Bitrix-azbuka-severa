<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (stripos($APPLICATION->GetCurPage(), '/order/') === false && stripos($APPLICATION->GetCurPage(), '/login/') === false && stripos($APPLICATION->GetCurPage(), '/lk/') === false) : ?>
	<? if (stripos($APPLICATION->GetCurPage(), '/cart/') === false && stripos($APPLICATION->GetCurPage(), '/search/') === false) : ?>
		<div class="subscribe">
			<div class="container">
				<div class="subscribe__block">
					<h2 class="caption--h2">Присоединяйтесь к нам</h2>
					<? /*<p class="caption__description">Подписывайтесь на нашу рассылку и первыми узнайте</p>
           <div class="subscribe__tag">
                <a href="/catalog/taezhnaya-zdravnitsa/maslo/" class="subscribe__tag-item subscribe__tag-item--red">Скидка 10 % на все масла</a>
                <a href="/catalog/ryba/slabosolenaya-ryba/tugunok-s-s/" class="subscribe__tag-item subscribe__tag-item--green">Свежайший Тугунок</a>
                <a href="/catalog/ryba/kholodnoe-kopchenie/nelma-krupnaya-sp-khk/" class="subscribe__tag-item subscribe__tag-item--orange">Только из Коптильни</a>
            </div>*/ ?>
					<? $APPLICATION->IncludeComponent(
						"bitrix:sender.subscribe",
						"main",
						array(
							"COMPONENT_TEMPLATE" => ".default",
							"USE_PERSONALIZATION" => "Y",
							"CONFIRMATION" => "N",
							"SHOW_HIDDEN" => "Y",
							"AJAX_MODE" => "Y",
							"AJAX_OPTION_JUMP" => "Y",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "Y",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "3600",
							"SET_TITLE" => "N"
						)
					); ?>
					<p class="subscribe__politic">Нажимая кнопку «Подписаться», вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></p>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="container">
		<div class="footer-top__wrapper" style="padding: 0;">
			<div class="header-announcement" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
				<span style="font-family: 'Open Sans';">Участник федерального проекта "Гастрономическая карта России"</span>
				<img src="<?= SITE_TEMPLATE_PATH ?>/assets/img/GastroMap_Logo.png" alt="GastroMap Logo" class="header-announcement_logo">
			</div>
		</div>
	</div>

	<button class="btn btn--blue go-to-head">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M11.4697 4.46964C11.7626 4.17674 12.2374 4.17674 12.5303 4.46964L18.5303 10.4696C18.8232 10.7625 18.8232 11.2374 18.5303 11.5303C18.2374 11.8232 17.7626 11.8232 17.4697 11.5303L12.75 6.81063V19C12.75 19.4142 12.4142 19.75 12 19.75C11.5858 19.75 11.25 19.4142 11.25 19V6.81063L6.53033 11.5303C6.23744 11.8232 5.76256 11.8232 5.46967 11.5303C5.17678 11.2374 5.17678 10.7625 5.46967 10.4696L11.4697 4.46964Z" fill="#fff"></path>
		</svg>
		<span>Наверх</span>
	</button>
	</div>

	<footer class="footer lazy-background">
		<div class="footer-top">
			<div class="container">
				<div class="footer-top__wrapper">
					<div class="footer-top__contact">
						<a href="tel:+74951210110" class="footer-top__contact-number">+7 (495) 121-01-10</a>
						<div class="footer-top__contact-btn">
							<button class="btn btn--outline-dark" data-modal="true" data-modal-id="#modal-callback">Перезвоните мне</button>
							<button class="btn btn--outline-dark" data-modal="true" data-modal-id="#send-message">Оставить сообщение</button>
						</div>
						<div class="footer-social">
							<?/*
                        <a href="https://www.instagram.com/azbuka_severa/" target="_blank" class="footer-social__item">
                            <svg class="icon--24" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.99143 14.7103C7.97794 14.7103 6.34566 13.0778 6.34566 11.0646C6.34566 9.05106 7.97794 7.41897 9.99143 7.41897C12.0049 7.41897 13.637 9.05106 13.637 11.0646C13.6372 13.0778 12.0051 14.7103 9.99143 14.7103Z" fill="#12266B"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 7.01031C0 3.57387 2.78578 0.788086 6.22222 0.788086H13.7778C17.2142 0.788086 20 3.57387 20 7.01031V14.5659C20 18.0023 17.2142 20.7881 13.7778 20.7881H6.22222C2.78578 20.7881 0 18.0023 0 14.5659V7.01031ZM9.99143 5.44812C6.88969 5.44812 4.375 7.96262 4.375 11.0644C4.375 14.1663 6.88969 16.6808 9.99143 16.6808C13.0934 16.6808 15.6077 14.1663 15.6077 11.0644C15.6079 7.96262 13.0934 5.44812 9.99143 5.44812ZM15.8299 6.53813C16.5547 6.53813 17.1423 5.9503 17.1423 5.22551C17.1423 4.50052 16.5547 3.91289 15.8299 3.91309C15.1051 3.91309 14.5173 4.50072 14.5173 5.22551C14.5173 5.9503 15.1049 6.53813 15.8299 6.53813Z" fill="#12266B"></path>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/severnayarybamsk" target="_blank" class="footer-social__item">
                            <svg class="icon--24" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 5.23253C0 2.77793 1.98985 0.788086 4.44444 0.788086H15.5556C18.0102 0.788086 20 2.77793 20 5.23253V16.3436C20 18.7982 18.0102 20.7881 15.5556 20.7881L13.364 20.7875V12.5939H15.5591L15.7931 9.85033H13.364V8.28781C13.364 7.64105 13.489 7.38538 14.0903 7.38538H15.793V4.53809H13.6144C11.2734 4.53809 10.2179 5.61048 10.2179 7.66298V9.85033H8.58127V12.6281H10.2178L10.2179 20.7875L4.44444 20.7881C1.98985 20.7881 0 18.7982 0 16.3436V5.23253Z" fill="#12266B"></path>
                            </svg>
                        </a>
*/ ?>
							<a href="https://www.youtube.com/channel/UCDirTrTbFRsfONHMKROunsg" target="_blank" class="footer-social__item">
								<svg class="icon--24" width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M21.3904 0.900232C22.4184 1.177 23.2289 1.98754 23.5057 3.01554C24.0197 4.89363 23.9999 8.80794 23.9999 8.80794C23.9999 8.80794 23.9999 12.7025 23.5057 14.5806C23.2289 15.6086 22.4184 16.4191 21.3904 16.6959C19.5123 17.1901 11.9999 17.1901 11.9999 17.1901C11.9999 17.1901 4.50739 17.1901 2.60954 16.6761C1.58154 16.3993 0.771002 15.5888 0.494232 14.5608C0 12.7025 0 8.78817 0 8.78817C0 8.78817 0 4.89363 0.494232 3.01554C0.771002 1.98754 1.60131 1.15723 2.60954 0.880462C4.48763 0.38623 11.9999 0.38623 11.9999 0.38623C11.9999 0.38623 19.5123 0.38623 21.3904 0.900232ZM15.8549 8.78817L9.60781 12.3862V5.19016L15.8549 8.78817Z" fill="#12266B"></path>
								</svg>
							</a>
							<a href="https://t.me/azbukasevera" target="_blank" class="footer-social__item">
								<svg class="icon--24" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M10 20.7881C15.5228 20.7881 20 16.3109 20 10.7881C20 5.26524 15.5228 0.788086 10 0.788086C4.47715 0.788086 0 5.26524 0 10.7881C0 16.3109 4.47715 20.7881 10 20.7881ZM10.7901 8.09069L4.74383 10.5819L4.74323 10.5819C3.67927 10.997 4.30208 11.3863 4.30208 11.3863C4.30208 11.3863 5.21034 11.6977 5.98878 11.9312C6.76721 12.1647 7.18247 11.9052 7.18247 11.9052L10.8414 9.44001C12.1388 8.55765 11.8274 9.28424 11.516 9.59564C10.8414 10.2703 9.72556 11.3343 8.79135 12.1906C8.37617 12.5538 8.58372 12.8653 8.76539 13.021C9.29358 13.4678 10.5373 14.2805 11.1025 14.6497C11.2593 14.7522 11.3638 14.8205 11.3864 14.8374C11.516 14.9412 12.2419 15.4083 12.6838 15.3045C13.1257 15.2007 13.1776 14.6039 13.1776 14.6039L13.8263 10.5299C13.8835 10.1512 13.9406 9.78043 13.994 9.43429C14.1345 8.52328 14.2486 7.78304 14.2674 7.51981C14.3453 6.63759 13.4111 7.00085 13.4111 7.00085C13.4111 7.00085 11.387 7.83121 10.7901 8.09069Z" fill="#12266B"></path>
								</svg>
							</a>
							<a href="https://vk.com/public211095515" target="_blank" class="footer-social__item">
								<svg width="281" height="160" viewBox="0 0 281 160" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M278.947 144.498C278.607 143.766 278.291 143.159 277.997 142.672C273.131 133.909 263.833 123.154 250.109 110.402L249.819 110.11L249.674 109.966L249.527 109.82H249.379C243.151 103.882 239.206 99.8892 237.554 97.8451C234.532 93.9512 233.855 90.0098 235.507 86.0162C236.674 82.999 241.057 76.6268 248.648 66.8912C252.64 61.7316 255.802 57.5964 258.137 54.4809C274.978 32.0916 282.28 17.7845 280.04 11.554L279.17 10.098C278.585 9.22148 277.077 8.41959 274.646 7.68828C272.21 6.95851 269.096 6.83782 265.299 7.32417L223.249 7.61464C222.567 7.37326 221.595 7.39576 220.328 7.68828C219.062 7.98081 218.429 8.12759 218.429 8.12759L217.697 8.49324L217.116 8.93253C216.629 9.22301 216.094 9.73391 215.509 10.4642C214.928 11.1919 214.441 12.046 214.053 13.0192C209.474 24.7974 204.269 35.7481 198.428 45.8709C194.826 51.907 191.518 57.1382 188.497 61.5675C185.48 65.9952 182.949 69.2575 180.907 71.3486C178.861 73.4418 177.015 75.1187 175.357 76.3855C173.702 77.6527 172.438 78.1882 171.563 77.9923C170.686 77.7964 169.86 77.6021 169.078 77.4077C167.716 76.5312 166.621 75.3391 165.795 73.8305C164.965 72.3218 164.407 70.423 164.115 68.1354C163.824 65.8464 163.652 63.8775 163.603 62.2221C163.558 60.5687 163.579 58.23 163.678 55.2127C163.779 52.1939 163.824 50.1514 163.824 49.0805C163.824 45.381 163.896 41.3659 164.041 37.0343C164.188 32.7027 164.308 29.2706 164.407 26.7422C164.507 24.2113 164.553 21.5336 164.553 18.7106C164.553 15.8877 164.381 13.6738 164.041 12.0669C163.706 10.4621 163.191 8.90441 162.512 7.39423C161.829 5.88558 160.83 4.71855 159.519 3.89007C158.205 3.06262 156.572 2.40597 154.628 1.91809C149.468 0.750551 142.898 0.118965 134.915 0.0207753C116.81 -0.173559 105.177 0.995513 100.018 3.52646C97.9741 4.59581 96.1244 6.0569 94.4705 7.90512C92.7179 10.0474 92.4735 11.2165 93.7387 11.4088C99.58 12.2838 103.715 14.377 106.149 17.6863L107.026 19.4399C107.708 20.7051 108.389 22.9451 109.071 26.1567C109.752 29.3683 110.191 32.9211 110.384 36.8129C110.87 43.9199 110.87 50.0036 110.384 55.0644C109.897 60.1273 109.437 64.0688 108.997 66.8917C108.558 69.7147 107.902 72.0022 107.026 73.7538C106.149 75.5058 105.565 76.5767 105.273 76.9654C104.981 77.3541 104.737 77.599 104.544 77.6952C103.279 78.18 101.963 78.427 100.601 78.427C99.2373 78.427 97.5834 77.7448 95.637 76.3814C93.6911 75.018 91.6716 73.1452 89.5784 70.76C87.4852 68.3743 85.1246 65.0404 82.4954 60.7579C79.8683 56.4754 77.1425 51.414 74.3196 45.5738L71.984 41.3383C70.5239 38.6135 68.5294 34.646 65.9985 29.4394C63.466 24.2307 61.2276 19.1924 59.2817 14.3253C58.5038 12.2812 57.3353 10.725 55.778 9.65413L55.0472 9.21483C54.5614 8.82616 53.7815 8.41346 52.7116 7.97365C51.6402 7.53435 50.5223 7.21932 49.3532 7.0255L9.34646 7.31598C5.25828 7.31598 2.48441 8.24214 1.02383 10.0919L0.439298 10.9669C0.147285 11.4543 0 12.2326 0 13.304C0 14.3749 0.292013 15.6892 0.87655 17.2454C6.71681 30.9716 13.068 44.2093 19.93 56.9607C26.792 69.7121 32.755 79.9837 37.8154 87.7663C42.8768 95.5545 48.0359 102.905 53.2926 109.814C58.5493 116.726 62.0289 121.155 63.7314 123.101C65.4359 125.05 66.7748 126.508 67.748 127.481L71.3989 130.985C73.735 133.321 77.1655 136.12 81.692 139.38C86.2195 142.642 91.2318 145.854 96.7314 149.02C102.232 152.181 108.631 154.76 115.933 156.755C123.233 158.753 130.338 159.554 137.25 159.167H154.042C157.447 158.873 160.027 157.802 161.781 155.954L162.362 155.222C162.752 154.641 163.118 153.739 163.456 152.524C163.797 151.307 163.967 149.967 163.967 148.509C163.867 144.324 164.186 140.553 164.914 137.195C165.642 133.838 166.471 131.307 167.399 129.602C168.326 127.899 169.373 126.462 170.537 125.297C171.704 124.13 172.536 123.423 173.023 123.179C173.508 122.934 173.895 122.768 174.187 122.668C176.524 121.89 179.273 122.644 182.439 124.933C185.604 127.221 188.571 130.045 191.347 133.402C194.122 136.762 197.455 140.532 201.348 144.716C205.243 148.903 208.649 152.015 211.568 154.062L214.487 155.814C216.437 156.983 218.968 158.054 222.083 159.027C225.193 160 227.918 160.243 230.257 159.757L267.635 159.174C271.332 159.174 274.208 158.562 276.249 157.347C278.293 156.131 279.508 154.79 279.9 153.332C280.29 151.873 280.311 150.217 279.974 148.365C279.628 146.519 279.286 145.227 278.947 144.498Z" fill="black" />
								</svg>
							</a>
							<a href="https://zen.yandex.ru/id/609af1134c808749b666c73c?lang=ru" target="_blank" class="footer-social__item">
								<svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="250px" height="250px">
									<path d="M46.894 23.986c.004 0 .007 0 .011 0 .279 0 .545-.117.734-.322.192-.208.287-.487.262-.769C46.897 11.852 38.154 3.106 27.11 2.1c-.28-.022-.562.069-.77.262-.208.192-.324.463-.321.746C26.193 17.784 28.129 23.781 46.894 23.986zM46.894 26.014c-18.765.205-20.7 6.202-20.874 20.878-.003.283.113.554.321.746.186.171.429.266.679.266.03 0 .061-.001.091-.004 11.044-1.006 19.787-9.751 20.79-20.795.025-.282-.069-.561-.262-.769C47.446 26.128 47.177 26.025 46.894 26.014zM22.823 2.105C11.814 3.14 3.099 11.884 2.1 22.897c-.025.282.069.561.262.769.189.205.456.321.734.321.004 0 .008 0 .012 0 18.703-.215 20.634-6.209 20.81-20.875.003-.283-.114-.555-.322-.747C23.386 2.173 23.105 2.079 22.823 2.105zM3.107 26.013c-.311-.035-.555.113-.746.321-.192.208-.287.487-.262.769.999 11.013 9.715 19.757 20.724 20.792.031.003.063.004.094.004.25 0 .492-.094.678-.265.208-.192.325-.464.322-.747C23.741 32.222 21.811 26.228 3.107 26.013z" />
								</svg>
							</a>
							<a href="https://www.tiktok.com/@azbuka_severa" target="_blank" class="footer-social__item">
								<svg width="20" height="20" viewBox="0 0 174 200" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M91.2935 0.168759C102.203 0 113.049 0.100005 123.883 0C124.539 12.7569 129.128 25.7514 138.468 34.7706C147.789 44.0148 160.974 48.2463 173.802 49.6776V83.2357C161.78 82.8419 149.702 80.3418 138.793 75.1665C134.042 73.0164 129.616 70.2475 125.283 67.4161C125.227 91.7674 125.383 116.087 125.127 140.339C124.477 151.989 120.632 163.584 113.855 173.184C102.953 189.166 84.0292 199.586 64.593 199.911C52.6712 200.592 40.7619 197.342 30.6031 191.354C13.7676 181.428 1.92086 163.259 0.195419 143.758C-0.00463171 139.589 -0.0733993 135.426 0.0953934 131.357C1.59577 115.5 9.44151 100.33 21.6196 90.0111C35.4231 77.9917 54.7592 72.2664 72.8638 75.654C73.0326 87.9984 72.5387 100.33 72.5387 112.675C64.2679 110 54.603 110.75 47.3761 115.769C42.0998 119.188 38.0925 124.425 36.0045 130.351C34.2791 134.576 34.7729 139.27 34.873 143.758C36.8547 157.433 50.008 168.928 64.0491 167.684C73.3577 167.584 82.2787 162.184 87.1299 154.277C88.6991 151.508 90.4558 148.677 90.5496 145.42C91.3685 130.513 91.0434 115.669 91.1435 100.762C91.2122 67.1661 91.0434 33.6643 91.2997 0.175009L91.2935 0.168759Z" fill="black" />
								</svg>
							</a>


							<?/*                        <a href="what" class="footer-social__item">
                            <svg class="icon--24" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.9964 10.3494C19.9986 10.4143 20 10.4806 20 10.547C20 15.9288 15.5864 20.308 10.1612 20.308C8.50128 20.308 6.85727 19.8877 5.40578 19.0926L5.3791 19.0775L0 20.7881L1.74064 15.6455L1.75145 15.6138L1.73415 15.585C0.811191 14.0705 0.323034 12.3282 0.323034 10.547C0.323034 10.4028 0.328082 10.2593 0.334571 10.1166C0.563147 4.8854 4.8794 0.788086 10.1619 0.788086C15.5403 0.788086 19.8601 4.96614 19.995 10.3004L19.9964 10.3494ZM6.0879 10.8137C5.69276 10.286 5.39063 9.70285 5.23632 9.17014C5.1534 8.87819 5.11374 8.6158 5.11374 8.36855C5.11374 7.28872 5.64228 6.73006 5.89682 6.46118L5.94585 6.40928C6.17803 6.1577 6.43977 6.13031 6.54144 6.13031C6.6222 6.13031 6.70296 6.13536 6.78155 6.14112C6.87962 6.14761 6.96687 6.15338 7.06493 6.14833C7.18607 6.13824 7.32667 6.13031 7.48531 6.50804C7.52541 6.60309 7.5765 6.72574 7.63329 6.86207C7.66738 6.94389 7.70351 7.03064 7.74056 7.11932C7.7545 7.15288 7.76847 7.18652 7.78242 7.22011C7.99286 7.72685 8.19891 8.22305 8.23881 8.30079C8.30803 8.44136 8.3138 8.54876 8.25684 8.6605L8.24602 8.68212C8.24375 8.68676 8.24151 8.69134 8.23929 8.69587C8.19227 8.79195 8.15719 8.86364 8.1083 8.9373C8.08451 8.97262 8.05855 9.00723 8.0225 9.04759C7.98861 9.08796 7.95327 9.13049 7.91722 9.17446L7.91301 9.17953C7.82638 9.28389 7.73791 9.39046 7.66341 9.46352C7.54083 9.58607 7.37354 9.75403 7.54443 10.0424C7.72182 10.343 8.20925 11.1099 8.91084 11.7292C9.70294 12.4296 10.398 12.7267 10.7323 12.8697L10.7337 12.8703C10.7942 12.8962 10.844 12.9178 10.8808 12.9351C11.1476 13.0692 11.3257 13.0462 11.4922 12.858C11.6047 12.7311 12.095 12.1624 12.2681 11.9072C12.3964 11.7176 12.5024 11.732 12.731 11.8142C12.9423 11.8899 14.0938 12.4514 14.3844 12.5949C14.4166 12.611 14.4473 12.6257 14.4767 12.6397C14.4895 12.6458 14.502 12.6517 14.5142 12.6576L14.5161 12.6585C14.6702 12.7323 14.7811 12.7855 14.8199 12.8472C14.8646 12.9229 14.8682 13.3842 14.6714 13.9328C14.4781 14.4734 13.5069 14.9939 13.093 15.0307C13.0181 15.038 12.955 15.0528 12.8945 15.067C12.8898 15.0681 12.885 15.0692 12.8803 15.0703C12.581 15.1445 12.2407 15.226 10.2116 14.4331C8.75437 13.8643 7.31081 12.6093 6.14774 10.8995C6.1428 10.8922 6.13817 10.8853 6.13381 10.8789C6.11396 10.8495 6.09972 10.8285 6.0879 10.8137Z" fill="#12266B"></path>
                            </svg></a>
*/ ?>
						</div>
					</div>
					<div class="footer-menu">
						<? $APPLICATION->IncludeComponent(
							"bitrix:menu",
							"bot",
							array(
								"ALLOW_MULTI_SELECT" => "N",
								"CHILD_MENU_TYPE" => "left",
								"DELAY" => "N",
								"MAX_LEVEL" => "1",
								"MENU_CACHE_GET_VARS" => array(""),
								"MENU_CACHE_TIME" => "360000",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_USE_GROUPS" => "N",
								"MENU_THEME" => "site",
								"ROOT_MENU_TYPE" => "bot1",
								"USE_EXT" => "N",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_USE_USERS" => "N"
							)
						); ?>
						<? $APPLICATION->IncludeComponent(
							"bitrix:menu",
							"bot",
							array(
								"ALLOW_MULTI_SELECT" => "N",
								"CHILD_MENU_TYPE" => "left",
								"DELAY" => "N",
								"MAX_LEVEL" => "1",
								"MENU_CACHE_GET_VARS" => array(""),
								"MENU_CACHE_TIME" => "360000",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_USE_GROUPS" => "N",
								"MENU_THEME" => "site",
								"ROOT_MENU_TYPE" => "bot2",
								"USE_EXT" => "N",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_USE_USERS" => "N"
							)
						); ?>
					</div>
					<? $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"footer_address", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "17",
		"IBLOCK_TYPE" => "services",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MEDIA_PROPERTY" => "",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"SEARCH_PAGE" => "/search/",
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SLIDER_PROPERTY" => "",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"TEMPLATE_THEME" => "blue",
		"USE_RATING" => "N",
		"USE_SHARE" => "N",
		"COMPONENT_TEMPLATE" => "footer_address",
		"PAGER_BASE_LINK" => "",
		"PAGER_PARAMS_NAME" => "arrPager"
	),
	false
); ?>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="container">
				<div class="footer-bottom__wrapper">
					<div class="footer-bottom__copyright">
						<p><a download="" href="/politica.pdf">Политика конфиденциальности</a></p>
            <p><a href="/sitemap/">Пользовательская карта сайта</a></p>
					</div>
					<div class="footer-bottom__develop">
						<?/*
                    <span>Разработка и дизайн</span>
                    <a href="https://fouro.ru">
                        <svg width="65" height="20" viewBox="0 0 65 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.2031 0H0V19.661H2.70833V11.3559H10.4948L12.526 9.32203V8.30508H2.70833V2.88136H13.2031V0Z" fill="#ADBBE9"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5508 20C23.337 20 26.4062 17.0405 26.4062 13.3898C26.4062 9.73914 23.337 6.77966 19.5508 6.77966C15.7646 6.77966 12.6953 9.73914 12.6953 13.3898C12.6953 17.0405 15.7646 20 19.5508 20ZM19.5508 17.6271C21.9347 17.6271 23.8672 15.73 23.8672 13.3898C23.8672 11.0496 21.9347 9.15254 19.5508 9.15254C17.1669 9.15254 15.2344 11.0496 15.2344 13.3898C15.2344 15.73 17.1669 17.6271 19.5508 17.6271Z" fill="#ADBBE9"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M58.1445 20C61.9307 20 65 17.0405 65 13.3898C65 9.73914 61.9307 6.77966 58.1445 6.77966C54.3584 6.77966 51.2891 9.73914 51.2891 13.3898C51.2891 17.0405 54.3584 20 58.1445 20ZM58.1445 17.6271C60.5284 17.6271 62.4609 15.73 62.4609 13.3898C62.4609 11.0496 60.5284 9.15254 58.1445 9.15254C55.7606 9.15254 53.8281 11.0496 53.8281 13.3898C53.8281 15.73 55.7606 17.6271 58.1445 17.6271Z" fill="#ADBBE9"></path>
                            <path d="M31.1458 7.11864H28.4375V17.7966L30.2995 19.661H39.6094V7.11864H36.5625V16.9492H31.1458V7.11864Z" fill="#ADBBE9"></path>
                            <path d="M45.3646 7.11864H42.487V19.661H45.3646V11.6949L49.2578 9.49153H51.2891V7.11864H48.2422L45.3646 8.81356V7.11864Z" fill="#ADBBE9"></path>
                        </svg>
                    </a>
		*/ ?>
						&copy; <?= date("Y") ?> &laquo;Азбука Севера&raquo;
					</div>
				</div>
			</div>
		</div>
		<div class="recaptcha-class">
			This site is protected by reCAPTCHA and the Google
			<a href="https://policies.google.com/privacy">Privacy Policy</a> and
			<a href="https://policies.google.com/terms">Terms of Service</a> apply.
		</div>
	</footer>




<?php else : ?>

	<div class="special-footer">
		<div class="container">
			<div class="special-footer__wrapper">
				<div class="footer-bottom__copyright">
					<p>&copy; <?= date("Y") ?> &laquo;Азбука Севера&raquo;</p>
					<a download="" href="/politica.pdf">Политика конфиденциальности</a>
				</div>
			</div>
		</div>
	</div>
	</div>
<?php endif; ?>

<?
// подключаем файл модуля капчи
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/captcha.php");
// создаем объект капчи
$cpt = new CCaptcha();
// достаём значение параметра captcha_password из главного модуля
$captchaPass = COption::GetOptionString("main", "captcha_password", "");
// если строка пуста, генерируем случайное значение
if (strlen($captchaPass) <= 0) {
	$captchaPass = randString(10);
	COption::SetOptionString("main", "captcha_password", $captchaPass);
}
$cpt->SetCodeCrypt($captchaPass);
?>

<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) { ?>

	<div class="modal modal-left" id="modal-callback">
		<div class="modal-left-wrapper">
			<div class="modal-left__caption caption--h1">Обратный Звонок</div>
			<p class="modal-left__description">Оставьте свой телефон и мы перезвоним вам в течение часа</p>
			<button style="display: none;" id="good-callback" class="btn btn--green btn--check">Заявка принята</button>
			<form id="callback_form" action="javascript:void(0)">
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Имя</p>
					<input type="text" name="CLIENT_NAME" class="input" placeholder="Константин">
				</div>
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Телефон</p>
					<input type="tel" name="CLIENT_PHONE" class="input" placeholder="+7(999)000-00-00">
				</div>

				<div class="checkout-block__form-item checkout-block__form-item--textarea">
					<p>Комментарий или пожелания к заказу</p>
					<textarea name="COMMENT" class="textarea" placeholder="Ваш комментарий ..."></textarea>
					<p class="checkout-block__form-item-desc">Не обязательно</p>
				</div>

				<div>
					<input name="captcha_code" value="<?= htmlspecialchars($cpt->GetCodeCrypt()); ?>" type="hidden">
					<input id="captcha_word" placeholder="капча" class="input" style="width: 50%" name="captcha_word" type="text">
					<img src="/bitrix/tools/captcha.php?captcha_code=<?= htmlspecialchars($cpt->GetCodeCrypt()); ?>" alt="Капча">
				</div>
				<br>
				<button id="callback_form_send" class="btn btn--blue">Отправить</button>

				<div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></div>
			</form>
		</div>
	</div>

	<div class="modal modal-left" id="modal-item-buy">
		<div class="modal-left-wrapper">
			<div class="modal-left__caption caption--h1">Ожидаем поставку</div>
			<p class="modal-left__description">Оставьте свой телефон и мы перезвоним вам в течение часа</p>
			<button style="display: none;" id="good-item" class="btn btn--green btn--check">Заявка принята</button>
			<form id="item-buy_form" action="javascript:void(0)">
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Имя</p>
					<input type="text" name="CLIENT_NAME" class="input" placeholder="Константин">
				</div>
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Телефон</p>
					<input type="tel" name="CLIENT_PHONE" class="input" placeholder="+7(999)000-00-00">
				</div>

				<div class="checkout-block__form-item checkout-block__form-item--textarea">
					<p>Комментарий или пожелания к заказу</p>
					<textarea name="COMMENT" class="textarea" placeholder="Ваш комментарий ..."></textarea>
					<p class="checkout-block__form-item-desc">Не обязательно</p>
				</div>

				<input type="hidden" id="send-item" name="ITEM">

				<button id="item_form_send" class="btn btn--blue">Отправить</button>

				<div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></div>
			</form>
		</div>
	</div>

	<div class="modal modal-left" id="modal-one-click">
		<div class="modal-left-wrapper">
			<div class="modal-left__caption caption--h1">Быстрый заказ</div>
			<p class="modal-left__description">Оставьте свой телефон и мы перезвоним вам в течение часа</p>
			<button style="display: none;" id="one_click_success" class="btn btn--green btn--check">Заявка принята</button>
			<form id="one_click_form" action="javascript:void(0)">
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Имя</p>
					<input type="text" name="CLIENT_NAME" class="input" placeholder="Константин">
				</div>
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Телефон</p>
					<input type="tel" name="CLIENT_PHONE" class="input" placeholder="+7(999)000-00-00">
				</div>

				<div class="checkout-block__form-item checkout-block__form-item--textarea">
					<p>Комментарий или пожелания к заказу</p>
					<textarea name="COMMENT" class="textarea" placeholder="Ваш комментарий ..."></textarea>
					<p class="checkout-block__form-item-desc">Не обязательно</p>
				</div>

				<input type="hidden" id="this-product" name="ITEM">

				<button id="one_click_send" class="btn btn--blue">Отправить</button>

				<div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></div>
			</form>
		</div>
	</div>

	<div class="modal modal-left" id="send-message">
		<div class="modal-left-wrapper">
			<div class="modal-left__caption caption--h1">оставить
				сообщение</div>
			<p class="modal-left__description">Задайте вопросы или оставьте пожелания. Мы обязательно ответим! </p>
			<button style="display: none;" id="good-message" class="btn btn--green btn--check">Заявка принята</button>
			<form id="message_form" action="javascript:void(0)">
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Имя</p>
					<input type="text" name="CLIENT_NAME" class="input" placeholder="Константин">
				</div>
				<div class="checkout-block__form-item checkout-block__form-item--wide">
					<p>Телефон</p>
					<input type="tel" name="CLIENT_PHONE" class="input" placeholder="+7(999)000-00-00">
				</div>

				<div class="checkout-block__form-item checkout-block__form-item--textarea">
					<p>Ваше сообщение</p>
					<textarea name="COMMENT" class="textarea" placeholder="Хочу узнать есть ли у вас подписка на свежие продукты? не нашел такого раздела на сайте, а очень хочется попробовать прям свежего"></textarea>
				</div>

				<button id="message_form_send" class="btn btn--blue">Отправить</button>

				<div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></div>
			</form>
		</div>
	</div>

	<div class="modal modal-default" id="modal-add-review">
		<div class="modal-wrapper">
			<div class="modal__caption caption--h3">Ваш отзыв</div>
			<form id="review-add" action="javascript:void(0)">
				<input type="hidden" name="elemid" id="revelemid" value="" />
				<div class="form-group">
					<textarea class="form-control input" placeholder="Написать отзыв" name="textreview"></textarea>
				</div>
				<button class="btn btn--blue">Сохранить</button>
			</form>
		</div>
	</div>

	<div class="modal modal-default" id="modal-delivery-terms">
		<div class="modal-wrapper">
			<div class="modal__caption caption--h3">Доставка и оплата</div>
			<div class="modal__content">
				<h4>Доставка</h4>
				<ul>
					<li>Ежедневно с 10:00 до 21:00</li>
					<li>Минимальный интервал доставки — от 20 мин. (при выборе доставки «блиц»)</li>
					<li>Минимальная сумма заказа — от 1000 руб.</li>
					<li>Доставка бесплатная — при заказе от 3500 руб.</li>
					<li>Регион доставки — Москва, Моск. область, СДЭК по России.</li>
				</ul>
				<h4>Оплата</h4>
				<ul>
					<li>Оплата осуществляется наличными или картой курьеру, при выборе способа доставки «Блиц» оплата производится авансом по QR коду.</li>
				</ul>
				<br>
				<a class="btn btn--blue" href="/delivery/" target="_blank">Подробные условия</a>
			</div>
		</div>
	</div>

<?php }
if ($NY) : ?>
	<script src="<?= SITE_TEMPLATE_PATH ?>/newyear/snow.js"></script>
<? endif;

$curPage = $APPLICATION->GetCurPage();

if (
	stripos($curPage, 'catalog') === false &&
	stripos($curPage, 'cart') === false &&
	stripos($curPage, 'community') === false &&
	stripos($curPage, 'wishlist') === false &&
	stripos($curPage, 'tags') === false
	&& stripos($curPage, 'search') === false
	&& stripos($curPage, 'personal') === false
	&& stripos($curPage, 'delivery') === false
	&& stripos($curPage, 'contacts') === false
	&& stripos($curPage, '/') === false
) : ?>
	</div>
<? endif;
if (strlen($GLOBALS["seoFilterKeywords"])) {
	$APPLICATION->SetPageProperty("keywords", $GLOBALS["seoFilterKeywords"]);
}
if (strlen($GLOBALS["seoFilterDescription"])) {
	$APPLICATION->SetPageProperty("description", $GLOBALS["seoFilterDescription"]);
}
if (strlen($GLOBALS["seoFilterTitle"])) {
	$APPLICATION->SetPageProperty("title", $GLOBALS["seoFilterTitle"]);
}
if (strlen($GLOBALS["seoFilterH1"])) {
	$APPLICATION->SetTitle($GLOBALS["seoFilterH1"]);
} ?>
</body>

</html>