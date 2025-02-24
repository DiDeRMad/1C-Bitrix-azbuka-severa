<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var SaleOrderAjax $component
 * @var string $templateFolder
 */

$context = Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$arParams['ALLOW_USER_PROFILES'] = $arParams['ALLOW_USER_PROFILES'] === 'Y' ? 'Y' : 'N';
$arParams['SKIP_USELESS_BLOCK'] = $arParams['SKIP_USELESS_BLOCK'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['SHOW_ORDER_BUTTON'])) {
    $arParams['SHOW_ORDER_BUTTON'] = 'final_step';
}

$arParams['HIDE_ORDER_DESCRIPTION'] = isset($arParams['HIDE_ORDER_DESCRIPTION']) && $arParams['HIDE_ORDER_DESCRIPTION'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_TOTAL_ORDER_BUTTON'] = $arParams['SHOW_TOTAL_ORDER_BUTTON'] === 'Y' ? 'Y' : 'N';
$arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] = $arParams['SHOW_PAY_SYSTEM_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_PAY_SYSTEM_INFO_NAME'] = $arParams['SHOW_PAY_SYSTEM_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_LIST_NAMES'] = $arParams['SHOW_DELIVERY_LIST_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_INFO_NAME'] = $arParams['SHOW_DELIVERY_INFO_NAME'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_DELIVERY_PARENT_NAMES'] = $arParams['SHOW_DELIVERY_PARENT_NAMES'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_STORES_IMAGES'] = $arParams['SHOW_STORES_IMAGES'] === 'N' ? 'N' : 'Y';

if (!isset($arParams['BASKET_POSITION']) || !in_array($arParams['BASKET_POSITION'], array('before', 'after'))) {
    $arParams['BASKET_POSITION'] = 'after';
}

$arParams['EMPTY_BASKET_HINT_PATH'] = isset($arParams['EMPTY_BASKET_HINT_PATH']) ? (string)$arParams['EMPTY_BASKET_HINT_PATH'] : '/';
$arParams['SHOW_BASKET_HEADERS'] = $arParams['SHOW_BASKET_HEADERS'] === 'Y' ? 'Y' : 'N';
$arParams['HIDE_DETAIL_PAGE_URL'] = isset($arParams['HIDE_DETAIL_PAGE_URL']) && $arParams['HIDE_DETAIL_PAGE_URL'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERY_FADE_EXTRA_SERVICES'] = $arParams['DELIVERY_FADE_EXTRA_SERVICES'] === 'Y' ? 'Y' : 'N';

$arParams['SHOW_COUPONS'] = isset($arParams['SHOW_COUPONS']) && $arParams['SHOW_COUPONS'] === 'N' ? 'N' : 'Y';

if ($arParams['SHOW_COUPONS'] === 'N') {
    $arParams['SHOW_COUPONS_BASKET'] = 'N';
    $arParams['SHOW_COUPONS_DELIVERY'] = 'N';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = 'N';
} else {
    $arParams['SHOW_COUPONS_BASKET'] = isset($arParams['SHOW_COUPONS_BASKET']) && $arParams['SHOW_COUPONS_BASKET'] === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_DELIVERY'] = isset($arParams['SHOW_COUPONS_DELIVERY']) && $arParams['SHOW_COUPONS_DELIVERY'] === 'N' ? 'N' : 'Y';
    $arParams['SHOW_COUPONS_PAY_SYSTEM'] = isset($arParams['SHOW_COUPONS_PAY_SYSTEM']) && $arParams['SHOW_COUPONS_PAY_SYSTEM'] === 'N' ? 'N' : 'Y';
}

$arParams['SHOW_NEAREST_PICKUP'] = $arParams['SHOW_NEAREST_PICKUP'] === 'Y' ? 'Y' : 'N';
$arParams['DELIVERIES_PER_PAGE'] = isset($arParams['DELIVERIES_PER_PAGE']) ? intval($arParams['DELIVERIES_PER_PAGE']) : 9;
$arParams['PAY_SYSTEMS_PER_PAGE'] = isset($arParams['PAY_SYSTEMS_PER_PAGE']) ? intval($arParams['PAY_SYSTEMS_PER_PAGE']) : 9;
$arParams['PICKUPS_PER_PAGE'] = isset($arParams['PICKUPS_PER_PAGE']) ? intval($arParams['PICKUPS_PER_PAGE']) : 5;
$arParams['SHOW_PICKUP_MAP'] = $arParams['SHOW_PICKUP_MAP'] === 'N' ? 'N' : 'Y';
$arParams['SHOW_MAP_IN_PROPS'] = $arParams['SHOW_MAP_IN_PROPS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_YM_GOALS'] = $arParams['USE_YM_GOALS'] === 'Y' ? 'Y' : 'N';
$arParams['USE_ENHANCED_ECOMMERCE'] = isset($arParams['USE_ENHANCED_ECOMMERCE']) && $arParams['USE_ENHANCED_ECOMMERCE'] === 'Y' ? 'Y' : 'N';
$arParams['DATA_LAYER_NAME'] = isset($arParams['DATA_LAYER_NAME']) ? trim($arParams['DATA_LAYER_NAME']) : 'dataLayer';
$arParams['BRAND_PROPERTY'] = isset($arParams['BRAND_PROPERTY']) ? trim($arParams['BRAND_PROPERTY']) : '';

$useDefaultMessages = !isset($arParams['USE_CUSTOM_MAIN_MESSAGES']) || $arParams['USE_CUSTOM_MAIN_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_BLOCK_NAME'])) {
    $arParams['MESS_AUTH_BLOCK_NAME'] = Loc::getMessage('AUTH_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REG_BLOCK_NAME'])) {
    $arParams['MESS_REG_BLOCK_NAME'] = Loc::getMessage('REG_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BASKET_BLOCK_NAME'])) {
    $arParams['MESS_BASKET_BLOCK_NAME'] = Loc::getMessage('BASKET_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_BLOCK_NAME'])) {
    $arParams['MESS_REGION_BLOCK_NAME'] = Loc::getMessage('REGION_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAYMENT_BLOCK_NAME'])) {
    $arParams['MESS_PAYMENT_BLOCK_NAME'] = Loc::getMessage('PAYMENT_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_BLOCK_NAME'])) {
    $arParams['MESS_DELIVERY_BLOCK_NAME'] = Loc::getMessage('DELIVERY_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BUYER_BLOCK_NAME'])) {
    $arParams['MESS_BUYER_BLOCK_NAME'] = Loc::getMessage('BUYER_BLOCK_NAME_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_BACK'])) {
    $arParams['MESS_BACK'] = Loc::getMessage('BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FURTHER'])) {
    $arParams['MESS_FURTHER'] = Loc::getMessage('FURTHER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_EDIT'])) {
    $arParams['MESS_EDIT'] = Loc::getMessage('EDIT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER'])) {
    $arParams['MESS_ORDER'] = $arParams['~MESS_ORDER'] = Loc::getMessage('ORDER_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PRICE'])) {
    $arParams['MESS_PRICE'] = Loc::getMessage('PRICE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERIOD'])) {
    $arParams['MESS_PERIOD'] = Loc::getMessage('PERIOD_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_BACK'])) {
    $arParams['MESS_NAV_BACK'] = Loc::getMessage('NAV_BACK_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NAV_FORWARD'])) {
    $arParams['MESS_NAV_FORWARD'] = Loc::getMessage('NAV_FORWARD_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ADDITIONAL_MESSAGES']) || $arParams['USE_CUSTOM_ADDITIONAL_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRICE_FREE'])) {
    $arParams['MESS_PRICE_FREE'] = Loc::getMessage('PRICE_FREE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ECONOMY'])) {
    $arParams['MESS_ECONOMY'] = Loc::getMessage('ECONOMY_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGISTRATION_REFERENCE'])) {
    $arParams['MESS_REGISTRATION_REFERENCE'] = Loc::getMessage('REGISTRATION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_1'])) {
    $arParams['MESS_AUTH_REFERENCE_1'] = Loc::getMessage('AUTH_REFERENCE_1_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_2'])) {
    $arParams['MESS_AUTH_REFERENCE_2'] = Loc::getMessage('AUTH_REFERENCE_2_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_AUTH_REFERENCE_3'])) {
    $arParams['MESS_AUTH_REFERENCE_3'] = Loc::getMessage('AUTH_REFERENCE_3_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ADDITIONAL_PROPS'])) {
    $arParams['MESS_ADDITIONAL_PROPS'] = Loc::getMessage('ADDITIONAL_PROPS_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_USE_COUPON'])) {
    $arParams['MESS_USE_COUPON'] = Loc::getMessage('USE_COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_COUPON'])) {
    $arParams['MESS_COUPON'] = Loc::getMessage('COUPON_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PERSON_TYPE'])) {
    $arParams['MESS_PERSON_TYPE'] = Loc::getMessage('PERSON_TYPE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PROFILE'])) {
    $arParams['MESS_SELECT_PROFILE'] = Loc::getMessage('SELECT_PROFILE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_REGION_REFERENCE'])) {
    $arParams['MESS_REGION_REFERENCE'] = Loc::getMessage('REGION_REFERENCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PICKUP_LIST'])) {
    $arParams['MESS_PICKUP_LIST'] = Loc::getMessage('PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_NEAREST_PICKUP_LIST'])) {
    $arParams['MESS_NEAREST_PICKUP_LIST'] = Loc::getMessage('NEAREST_PICKUP_LIST_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SELECT_PICKUP'])) {
    $arParams['MESS_SELECT_PICKUP'] = Loc::getMessage('SELECT_PICKUP_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_INNER_PS_BALANCE'])) {
    $arParams['MESS_INNER_PS_BALANCE'] = Loc::getMessage('INNER_PS_BALANCE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_ORDER_DESC'])) {
    $arParams['MESS_ORDER_DESC'] = Loc::getMessage('ORDER_DESC_DEFAULT');
}

$useDefaultMessages = !isset($arParams['USE_CUSTOM_ERROR_MESSAGES']) || $arParams['USE_CUSTOM_ERROR_MESSAGES'] != 'Y';

if ($useDefaultMessages || !isset($arParams['MESS_PRELOAD_ORDER_TITLE'])) {
    $arParams['MESS_PRELOAD_ORDER_TITLE'] = Loc::getMessage('PRELOAD_ORDER_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_SUCCESS_PRELOAD_TEXT'])) {
    $arParams['MESS_SUCCESS_PRELOAD_TEXT'] = Loc::getMessage('SUCCESS_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_FAIL_PRELOAD_TEXT'])) {
    $arParams['MESS_FAIL_PRELOAD_TEXT'] = Loc::getMessage('FAIL_PRELOAD_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TITLE'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] = Loc::getMessage('DELIVERY_CALC_ERROR_TITLE_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_DELIVERY_CALC_ERROR_TEXT'])) {
    $arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] = Loc::getMessage('DELIVERY_CALC_ERROR_TEXT_DEFAULT');
}

if ($useDefaultMessages || !isset($arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'])) {
    $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] = Loc::getMessage('PAY_SYSTEM_PAYABLE_ERROR_DEFAULT');
}

$scheme = $request->isHttps() ? 'https' : 'http';

switch (LANGUAGE_ID) {
    case 'ru':
        $locale = 'ru-RU';
        break;
    case 'ua':
        $locale = 'ru-UA';
        break;
    case 'tk':
        $locale = 'tr-TR';
        break;
    default:
        $locale = 'en-US';
        break;
}

$this->addExternalJs($templateFolder . '/order_ajax.js');
\Bitrix\Sale\PropertyValueCollection::initJs();
$this->addExternalJs($templateFolder . '/script.js');
?><NOSCRIPT>
    <div style="color:red"><?= Loc::getMessage('SOA_NO_JS') ?></div>
</NOSCRIPT>
<?

if ($request->get('ORDER_ID') <> '') {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/confirm.php');
} elseif ($arParams['DISABLE_BASKET_REDIRECT'] === 'Y' && $arResult['SHOW_EMPTY_BASKET']) {
    include(Main\Application::getDocumentRoot() . $templateFolder . '/empty.php');
} else {

    Main\UI\Extension::load('phone_auth');


    $themeClass = !empty($arParams['TEMPLATE_THEME']) ? ' bx-' . $arParams['TEMPLATE_THEME'] : '';
    $hideDelivery = empty($arResult['DELIVERY']);

    if ($USER->IsAuthorized()) {
        $arUser = $USER->GetByID($USER->GetId())->fetch();
        $userAddress = $arUser["UF_ADDRESSES"][0];

        $arMakeAddress = explode(', ', $userAddress);
        foreach ($arMakeAddress as $key => $addressField) {
            $arCheckField = explode(' ', $addressField);
            switch ($arCheckField[0]) {
                case 'г':
                case 'г.':
                    unset($arCheckField[0]);
                    $arAddress['town'] = implode(' ', $arCheckField);
                    break;
                case 'ул':
                case 'ул.':
                    unset($arCheckField[0]);
                    $arAddress['street'] = implode(' ', $arCheckField);
                    break;
                case 'корп':
                case 'корп.':
                case 'корпус':
                case 'дои':
                case 'дом.':
                case 'стр':
                case 'стр.':
                case 'строение':
                case 'д':
                case 'д.':
                case 'к':
                case 'к.':
                    unset($arCheckField[0]);
                    $arAddress['house'] = implode(' ', $arCheckField);
                    break;
                case 'квартира':
                case 'кв':
                case 'кв.':
                    unset($arCheckField[0]);
                    $arAddress['kv'] = implode(' ', $arCheckField);
                    break;
                case 'п.':
                case 'подъезд':
                case 'под':
                case 'под.':
                    unset($arCheckField[0]);
                    $arAddress['pod'] = implode(' ', $arCheckField);
                    break;
                case 'эт.':
                case 'этаж':
                    unset($arCheckField[0]);
                    $arAddress['floor'] = implode(' ', $arCheckField);
                    break;
                default:
                    if (count($arCheckField) > 1) {
                        foreach ($arCheckField as $fieldCheck) {
                            $arAddress['street'] = $arAddress['street'] . ' ' . $fieldCheck;
                        }
                    }
                    break;
            }
        }
    }
    ?>
	
	<style>
	
	.ui-menu {
		
	list-style-type: none;	
	
	padding-top: 3px;
	padding-left: 20px;
	
	margin-top: 10px;
	
	position: relative;
	
	
	background-color: white;
	}
	
	li.ui-menu-item {
		
	margin-top: 12px;
    margin-bottom: 12px;

    cursor: pointer;	
	}
	
	.city_p {
		
	margin-top: 8px;
    margin-bottom: 8px;	
	}
	
	.city_p button {
		
	width: 250px;
    height: 40px;

    color: #fff;
	
    background: #1b389e;
	
    text-align: center;
	
    line-height: 40px;	
	}
	
	#guess_region.choose_city {
		
	height: 385px !important;	
	}
	
	.outer_p_with_text_info {
		
	width: 470px; 
	
	font-size: 85%; 
	
	padding-left: 10px; 
	
	color: #b2b9ce !important;	
	}
	
	.sum_of_zakaz {
		
	display: inline-block; 
	
	width: 110px; 
	
	font-size: 94%; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka {

    font-size: 94%; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span {
		
	display: inline-block; 
	
	width: 110px; 
	
	color: #b2b9ce !important;	
	}
	
	.stoimost_dostavka + br + span + span {
		
	display: inline-block; 
	
	width: 125px; 
	
	text-align: center; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span + span + br + span {
		
	display: inline-block; 
	
	width: 110px; 
	
	color: #b2b9ce !important;		
	}
	
	.stoimost_dostavka + br + span + span + br + span + span {
		
	display: inline-block; 
	
	width: 125px; 
	
	text-align: center; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span + span + br + span + span + br + span {
		
	display: inline-block; 
	
	width: 110px; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span + span + br + span + span + br + span + span {
		
	display: inline-block; 
	
	width: 125px; 
	
	text-align: center; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span + span + br + span + span + br + span + span + br + span {
		
	display: inline-block; 
	
	width: 110px; 
	
	color: #b2b9ce !important;
	}
	
	.stoimost_dostavka + br + span + span + br + span + span + br + span + span + br + span + span {
	
	display: inline-block; 
	
	width: 125px; 
	
	text-align: center; 
	
	color: #b2b9ce !important;
	}
	
	.ui-helper-hidden-accessible {
		
	display: none !important;	
	}
	
	</style>
	
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
	
    <div id="container-peterburg" class="container peterburg" style="position: relative; display: none;">
	
        <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" name="ORDER_FORM" id="bx-soa-order-form"
              enctype="multipart/form-data">

            <?
            echo bitrix_sessid_post();

            if ($arResult['PREPAY_ADIT_FIELDS'] <> '') {
                echo $arResult['PREPAY_ADIT_FIELDS'];
            }
			
			$ip_address = $_SERVER['REMOTE_ADDR'];

			$ch = curl_init('https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip=' . $ipAddress);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Token dfb3e71f9984667dcbea2b702ac83971750adad5'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);

			$res = json_decode($res, true);
			$region = $res['location']['data']['region'];
			$city = $res['location']['data']['city'];
			
            ?>
			
            <input type="hidden" name="<?= $arParams['ACTION_VARIABLE'] ?>" value="saveOrderAjax">
            <input type="hidden" name="location_type" value="code">
            <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $arResult['BUYER_STORE'] ?>">
            <div id="bx-soa-order" class="">

                <div class="content-errors"></div>
                <div class="checkout-block">

                    <div class="checkout-block__content">
					
					    <div class="checkout-block-item checkout-block-item__recipient">
                            <p class="checkout-block__title">Получатель</p>

                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item">
                                    <p>Имя*</p>
                                    <input id="name" type="text" class="input name_spb"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetFirstName() : '' ?>">
                                </div>
                                <div class="checkout-block__form-item">
                                    <p>Фамилия</p>
                                    <input id="family" type="text" class="input surname_spb"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetLastName() : '' ?>">
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row">

                                <div class="checkout-block__form-item">
                                    <p>Телефон*</p>
                                    <input id="phone" type="text" class="input js-phone-validation js-phone-mask phone_spb"
                                           value="<?= $USER->IsAuthorized() ? $arUser['PERSONAL_PHONE'] : '' ?>">
                                    <span class="err-msg">Некорректно заполнены поля</span>
                                </div>
                                <div class="checkout-block__form-item">
                                    <p>E-mail*</p>
                                    <input id="email" type="email" class="input js-email-validation email_spb"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetParam('EMAIL') : '' ?>">
                                    <span class="err-msg">Некорректно заполнены поля</span>
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item checkout-block__form-item--textarea">
                                    <p>Комментарий или пожелания к заказу</p>
                                    <textarea id="comment" class="textarea comment_spb"
                                              placeholder="Ваш комментарий ..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block-item">
                            <p class="checkout-block__title">Способ доставки</p>

                            <div class="checkout-block-item__method">
                                <div class="checkout-block-item__method-list">
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Первая зона (зелёная)" data-price="400">
                                                <label for="m20" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="23" id="m20">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Первая зона (зелёная)</span>
                                                            <span class="checkout-block-item__method-price">от 400 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>1200 ₽</span><br><span>10000-20000 ₽</span><span>1000 ₽</span><br><span>20000-30000 ₽</span><span>800 ₽</span><br><span>Более 30000 ₽</span><span>400 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Вторая зона (жёлтая)" data-price="400">
                                                <label for="m21" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="24" id="m21">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Вторая зона (жёлтая)</span>
                                                            <span class="checkout-block-item__method-price">от 400 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>1200 ₽</span><br><span>10000-20000 ₽</span><span>1000 ₽</span><br><span>20000-30000 ₽</span><span>800 ₽</span><br><span>Более 30000 ₽</span><span>400 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Третья зона (оранжевая) " data-price="500">
                                                <label for="m22" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="26" id="m22">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Третья зона (оранжевая)</span>
                                                            <span class="checkout-block-item__method-price">от 500 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>1300 ₽</span><br><span>10000-20000 ₽</span><span>1100 ₽</span><br><span>20000-30000 ₽</span><span>900 ₽</span><br><span>Более 30000 ₽</span><span>500 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Четвёртая зона (красная) " data-price="600">
                                                <label for="m23" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="27" id="m23">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Четвёртая зона (красная)</span>
                                                            <span class="checkout-block-item__method-price">от 600 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>1400 ₽</span><br><span>10000-20000 ₽</span><span>1200 ₽</span><br><span>20000-30000 ₽</span><span>1000 ₽</span><br><span>Более 30000 ₽</span><span>600 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Пятая зона (голубая)" data-price="1400">
                                                <label for="m24" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="28" id="m24">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Пятая зона (голубая)</span>
                                                            <span class="checkout-block-item__method-price">от 1400 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>2200 ₽</span><br><span>10000-20000 ₽</span><span>2000 ₽</span><br><span>20000-30000 ₽</span><span>1800 ₽</span><br><span>Более 30000 ₽</span><span>1400 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Шестая зона (синяя)" data-price="1900">
                                                <label for="m25" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="29" id="m25">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Шестая зона (синяя)</span>
                                                            <span class="checkout-block-item__method-price">от 1900 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>2700 ₽</span><br><span>10000-20000 ₽</span><span>2500 ₽</span><br><span>20000-30000 ₽</span><span>2300 ₽</span><br><span>Более 30000 ₽</span><span>1900 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="Седьмая зона (серая)" data-price="2400">
                                                <label for="m26" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="30" id="m26">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">Седьмая зона (серая)</span>
                                                            <span class="checkout-block-item__method-price">от 2400 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info"><span class="sum_of_zakaz">Сумма заказа</span><span class="stoimost_dostavka">Стоимость доставки</span><br><span>5000-10000 ₽</span><span>3200 ₽</span><br><span>10000-20000 ₽</span><span>3000 ₽</span><br><span>20000-30000 ₽</span><span>2800 ₽</span><br><span>Более 30000 ₽</span><span>2400 ₽</span></p>
                                </div>
								
								<div class="checkout-block-item__delivery-wrapper" data-name="СДЭК" data-price="0">
                                                <label for="m27" class="checkout-block-item__method-item js-delivery-methods">
                                                    <input type="radio" name="method_delivery" value="31" id="m27">
                                                    <div>
                                                            <span class="checkout-block-item__method-info">СДЭК</span>
                                                            <span class="checkout-block-item__method-price">от 200 ₽</span>
                                                    </div>
                                                </label>
                                                <p class="checkout-block-item__method-description outer_p_with_text_info">Доставка осуществляется транспортной компанией СДЭК. <br> Стоимость зависит от расстояния и габаритов посылки. <br> Точную стоимость доставки сообщит менеджер.</p>
                                </div>
								
								<br>
								
								<p>*Если в заказе есть товары, требующие транспортировки в двух разных температурных режимах, то стоимость доставки увеличивается на 500 р</p>
								
								<br>
								
                                </div>
								
								<iframe src="https://yandex.ru/map-widget/v1/?ll=30.354698%2C59.914147&mode=usermaps&source=constructorLink&um=constructor%3Ae89274c4944271173fd4801bcd0cecde103f923d05c76f625bbd3219df036583&utm_source=share&z=8" width="100%" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe>
								
                            </div>
                        </div>
						
						<div class="checkout-block-item">
                            <p class="checkout-block__title">Адрес доставки</p>
                            <div class="form-group form-group_lg">
                                <?php foreach ($arUser["UF_ADDRESSES"] as $key => $uAddress) { ?>
                                    <div class="form-group">
                                        <div class="radio-tile">
                                            <input type="radio" name="ADDRESS" value="<?= $uAddress ?>"
                                                   id="uaddr_<?= $key ?>" <?= $key == 0 ? ' checked=""' : '' ?> />
                                            <label for="uaddr_<?= $key ?>" class="radio-tile__label">
                                                <span class="radio-tile__name"><?= $arUser['UF_UF_ADDRESSES_NAMES'][$key] ?></span>
                                                <span class="radio-tile__text"><?= $uAddress ?></span>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item">
                                    <p>Город</p>
                                    <input id="city" type="text" class="input spb_city"
                                           placeholder="Введите город" <?= $arAddress ? 'value="' . $arAddress['town'] . '"' : '' ?> <?php if ( ($city) && (!$arAddress) ) { echo 'value="' . $city . '"'; } ?>
                                           required>
                                </div>

                                <div class="checkout-block__form-item checkout-block__form-item--wide">
                                    <p>Адрес</p>
                                    <input id="address" type="text" class="input spb_address"
                                           placeholder="Введите улицу" <?= $arAddress ? 'value="' . $arAddress['street'] . '"' : '' ?>
                                           required>
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row checkout-block-item__form-row--small">
                                <div class="checkout-block__form-item checkout-block__form-item--normal">
                                    <p>Дом, строение, корпус</p>
                                    <input autocomplete="off" id="home" type="text"
                                           class="input home_spb" <?= $arAddress ? 'value="' . $arAddress['house'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Подъезд</p>
                                    <input autocomplete="off" id="podezd" type="text"
                                           class="input podezd_spb" <?= $arAddress ? 'value="' . $arAddress['pod'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Этаж</p>
                                    <input autocomplete="off" id="floor" type="text"
                                           class="input floor_spb" <?= $arAddress ? 'value="' . $arAddress['floor'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Квартира</p>
                                    <input autocomplete="off" id="apartment" type="text"
                                           class="input apartment_spb" <?= $arAddress ? 'value="' . $arAddress['kv'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Домофон</p>
                                    <input autocomplete="off" id="domofon" type="text"
                                           class="input domofon_spb" <?= $arAddress ? 'value="' . $arAddress['domofon'] . '"' : '' ?> >
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block-item">
                            <p class="checkout-block__title" style="margin-bottom: 10px;">Способ оплаты</p>
							<label for="p111" class="checkout-block-item__method-item" style="width: 150px;"><input type="radio" name="method_payment" value="11" id="p111" checked><div><span class="checkout-block-item__method-info">по QR коду</span></div></label>
							<!-- <div style="background-color: #1b389e; width: 122px; height: 40px; color: #fff; text-align: center; border-radius: 50px; box-sizing: border-box; padding-top: 8px; zoom: 1.19; padding-left: 10px; padding-right: 10px;"><span class="checkout-block-item__method-info" style="color: #fff;">по QR коду</span></div> -->
							<p style="margin-top: 10px;">*менеджер отправит вам QR код после согласования заказа</p>
                        </div>

                    </div>
                    <div id="bx-soa-orderSave" class="checkout-block__total checkout-total">
                        <div class="checkout-total__header">
                            <p class="checkout-total__caption">Итого</p>
                            <p data-price="<?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] ?>"
                               data-price-items="<?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] ?>"
                               class="checkout-total__total"><?= $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE_FORMATED'] ?></p>
                        </div>

                        <div class="checkout-total__content">
                            <? if ($arResult['JS_DATA']['TOTAL']['ORDER_WEIGHT']): ?>
                                <div class="checkout-total__row">
                                    <span>Вес</span>
                                    <span><?= $arResult['JS_DATA']['TOTAL']['ORDER_WEIGHT_FORMATED'] ?></span>
                                </div>
                            <? endif; ?>
                            <div class="checkout-total__row">
                                <span>Товары</span>
                                <span><?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE_FORMATED'] ?></span>
                            </div>
                            <div class="checkout-total__row">
                                <div>
                                    <span>Доставка</span>
                                    <p class="checkout-total__delivery-type"><?= current($arResult['DELIVERY'])['NAME'] ?></p>
                                </div>
                                <span class="checkout-total--green"><?= current($arResult['DELIVERY'])['PRICE_FORMATED'] ?></span>
                            </div>
                            <!--<div class="checkout-total__row">
                                <span>Промокод</span>
                                <span class="checkout-total--red">-1 000₽</span>
                            </div>

                            <label class="label--promo label--promo-default" for="">
                                <input type="text" class="input input--promo" placeholder="Промокод">
                                <span class="label--promo__icon label--promo__icon-check"></span>
                                <span class="label--promo__icon label--promo__icon-remove"></span>
                                <span class="label--promo__icon label--promo__icon-plus js-promo-add"></span>
                                <p class="label--promo-desc">Убрать промокод</p>
                                <p class="label--promo-desc-error">Промокод истек</p>
                            </label>-->

                            <button data-save-button="true" class="btn btn--green checkout-total__send">Отправить заказ
                            </button>

                            <div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download=""
                                                                                                         href="/politica.pdf">Условиями
                                    обработки
                                    перс. данных</a>, а также с <a download="" href="/oferta.pdf">Условиями продажи</a>
                            </div>

                            <?
                            global $USER;
                            if ($USER->IsAdmin()) {
                                ?>
                                <label class="filter__content-checkbox" for="subscribe_agree" style="margin-top: 20px;">
                                    <input type="checkbox" value="Y" name="subscribe_agree" id="subscribe_agree"
                                           class="form-check-input" checked>
                                    <span style="font-size: 14px;color: #b2b9ce;font-weight: 500;">Подписаться на рассылку</span>
                                </label>
                                <?
                            };
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
		
    </div>

	<div id="container-moscow" class="container moscow" style="display: none;">
        <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" name="ORDER_FORM" id="bx-soa-order-form"
              enctype="multipart/form-data">

            <?
            echo bitrix_sessid_post();

            if ($arResult['PREPAY_ADIT_FIELDS'] <> '') {
                echo $arResult['PREPAY_ADIT_FIELDS'];
            }
            ?>
            <input type="hidden" name="<?= $arParams['ACTION_VARIABLE'] ?>" value="saveOrderAjax">
            <input type="hidden" name="location_type" value="code">
            <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?= $arResult['BUYER_STORE'] ?>">
            <div id="bx-soa-order" class="">

                <div class="content-errors"></div>
                <div class="checkout-block">

                    <div class="checkout-block__content">
                        <div class="checkout-block-item">
                            <p class="checkout-block__title">Адрес доставки</p>
                            <div class="form-group form-group_lg">
                                <?php foreach ($arUser["UF_ADDRESSES"] as $key => $uAddress) { ?>
                                    <div class="form-group">
                                        <div class="radio-tile">
                                            <input type="radio" name="ADDRESS" value="<?= $uAddress ?>"
                                                   id="uaddr_<?= $key ?>" <?= $key == 0 ? ' checked=""' : '' ?> />
                                            <label for="uaddr_<?= $key ?>" class="radio-tile__label">
                                                <span class="radio-tile__name"><?= $arUser['UF_UF_ADDRESSES_NAMES'][$key] ?></span>
                                                <span class="radio-tile__text"><?= $uAddress ?></span>
                                            </label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item">
                                    <p>Город</p>
                                    <input id="city" type="text" class="input msk_city"
                                           placeholder="Введите город" <?php if ( ($city) && (!$arAddress) ) { echo 'value="' . $city . '"'; } ?> <?= $arAddress ? 'value="' . $arAddress['town'] . '"' : '' ?>
                                           required>
                                </div>

                                <div class="checkout-block__form-item checkout-block__form-item--wide">
                                    <p>Адрес</p>
                                    <input id="address" type="text" class="input msk_address"
                                           placeholder="Введите улицу" <?= $arAddress ? 'value="' . $arAddress['street'] . '"' : '' ?>
                                           required>
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row checkout-block-item__form-row--small">
                                <div class="checkout-block__form-item checkout-block__form-item--normal">
                                    <p>Дом, строение, корпус</p>
                                    <input autocomplete="off" id="home" type="text"
                                           class="input home_moscow home_msk" <?= $arAddress ? 'value="' . $arAddress['house'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Подъезд</p>
                                    <input autocomplete="off" id="podezd" type="text"
                                           class="input podezd_msk" <?= $arAddress ? 'value="' . $arAddress['pod'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Этаж</p>
                                    <input autocomplete="off" id="floor" type="text"
                                           class="input floor_msk" <?= $arAddress ? 'value="' . $arAddress['floor'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Квартира</p>
                                    <input autocomplete="off" id="apartment" type="text"
                                           class="input apartment_msk" <?= $arAddress ? 'value="' . $arAddress['kv'] . '"' : '' ?>
                                           required>
                                </div>
                                <div class="checkout-block__form-item checkout-block__form-item--small">
                                    <p>Домофон</p>
                                    <input autocomplete="off" id="domofon" type="text"
                                           class="input domofon_msk" <?= $arAddress ? 'value="' . $arAddress['domofon'] . '"' : '' ?> >
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block-item">
                            <p class="checkout-block__title">Способ доставки</p>

                            <div class="checkout-block-item__method">
                                <div class="checkout-block-item__method-list">
                                    <? foreach ($arResult['DELIVERY'] as $key => $arDelivery): ?>
                                        <? if ($arDelivery['ID'] != '3') { ?>
                                            <div <? if (in_array($arDelivery['ID'], $arResult['ONLY_MOSCOW'])): ?> data-only-moscow="Y" <? elseif (in_array($arDelivery['ID'], $arResult['NO_MOSCOW'])): ?> data-no-moscow="Y" <? endif ?>

                                                    class="checkout-block-item__delivery-wrapper <? if (array_key_first($arResult['DELIVERY']) == $key) echo 'active ' ?>"
                                                    data-name="<?= $arDelivery['NAME'] ?>"
                                                    data-price="<?= $arDelivery['PRICE'] ?>" <?php if ( ( strpos($arDelivery['NAME'], " зона ") !== FALSE ) || ( strpos($arDelivery['NAME'], "СДЭК") !== FALSE ) ) { echo "style='display: none !important' "; } ?> >
                                                <label for="m<?= $key ?>"
                                                       class="checkout-block-item__method-item js-delivery-methods">
                                                    <input <? if (array_key_first($arResult['DELIVERY']) == $key) echo 'checked="checked"' ?>
                                                            type="radio" name="method_delivery"
                                                            value="<?= $arDelivery['ID'] ?>" id="m<?= $key ?>">
                                                    <? if (!empty($arDelivery['NAME'])) { ?>
                                                        <div>
                                                            <span class="checkout-block-item__method-info"><?= $arDelivery['NAME'] ?></span>
                                                            <span class="checkout-block-item__method-price"><?= $arDelivery['PRICE'] ? $arDelivery['PRICE_FROM'] . $arDelivery['PRICE_FORMATED'] : 'бесплатно' ?></span>
                                                        </div>
                                                    <? } ?>
                                                </label>
                                                <p class="checkout-block-item__method-description"><?= $arDelivery['DESCRIPTION'] ?></p>
                                            </div>
                                        <? } ?>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block-item checkout-block-item__recipient">
                            <p class="checkout-block__title">Получатель</p>

                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item">
                                    <p>Имя*</p>
                                    <input id="name" type="text" class="input name_msk"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetFirstName() : '' ?>">
                                </div>
                                <div class="checkout-block__form-item">
                                    <p>Фамилия</p>
                                    <input id="family" type="text" class="input surname_msk"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetLastName() : '' ?>">
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row">

                                <div class="checkout-block__form-item">
                                    <p>Телефон*</p>
                                    <input id="phone" type="text" class="input js-phone-validation js-phone-mask phone_msk"
                                           value="<?= $USER->IsAuthorized() ? $arUser['PERSONAL_PHONE'] : '' ?>">
                                    <span class="err-msg">Некорректно заполнены поля</span>
                                </div>
                                <div class="checkout-block__form-item">
                                    <p>E-mail*</p>
                                    <input id="email" type="email" class="input js-email-validation email_msk"
                                           value="<?= $USER->IsAuthorized() ? $USER->GetParam('EMAIL') : '' ?>">
                                    <span class="err-msg">Некорректно заполнены поля</span>
                                </div>
                            </div>

                            <div class="checkout-block-item__form-row">
                                <div class="checkout-block__form-item checkout-block__form-item--textarea">
                                    <p>Комментарий или пожелания к заказу</p>
                                    <textarea id="comment" class="textarea comment_msk"
                                              placeholder="Ваш комментарий ..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block-item">
                            <p class="checkout-block__title">Способ оплаты</p>

                            <div class="checkout-block-item__method checkout-block-item__method-pay">
                                <div class="checkout-block-item__method-list">
                                    <? foreach ($arResult['PAY_SYSTEM'] as $arPaySystem): ?>
                                        <label for="p<?= $arPaySystem['ID'] ?>"
                                               class="checkout-block-item__method-item">
                                            <input type="radio" name="method_payment" value="<?= $arPaySystem['ID'] ?>"
                                                   id="p<?= $arPaySystem['ID'] ?>">
                                            <div>
                                                <span class="checkout-block-item__method-info"><?= $arPaySystem['NAME'] ?></span>
                                            </div>
                                        </label>
                                    <?endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="bx-soa-orderSave" class="checkout-block__total checkout-total">
                        <div class="checkout-total__header">
                            <p class="checkout-total__caption">Итого</p>
                            <p data-price="<?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] ?>"
                               data-price-items="<?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] ?>"
                               class="checkout-total__total"><?= $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE_FORMATED'] ?></p>
                        </div>

                        <div class="checkout-total__content">
                            <? if ($arResult['JS_DATA']['TOTAL']['ORDER_WEIGHT']): ?>
                                <div class="checkout-total__row">
                                    <span>Вес</span>
                                    <span><?= $arResult['JS_DATA']['TOTAL']['ORDER_WEIGHT_FORMATED'] ?></span>
                                </div>
                            <? endif; ?>
                            <div class="checkout-total__row">
                                <span>Товары</span>
                                <span><?= $arResult['JS_DATA']['TOTAL']['ORDER_PRICE_FORMATED'] ?></span>
                            </div>
                            <div class="checkout-total__row">
                                <div>
                                    <span>Доставка</span>
                                    <p class="checkout-total__delivery-type"><?= current($arResult['DELIVERY'])['NAME'] ?></p>
                                </div>
                                <span class="checkout-total--green"><?= current($arResult['DELIVERY'])['PRICE_FORMATED'] ?></span>
                            </div>
                            <!--<div class="checkout-total__row">
                                <span>Промокод</span>
                                <span class="checkout-total--red">-1 000₽</span>
                            </div>

                            <label class="label--promo label--promo-default" for="">
                                <input type="text" class="input input--promo" placeholder="Промокод">
                                <span class="label--promo__icon label--promo__icon-check"></span>
                                <span class="label--promo__icon label--promo__icon-remove"></span>
                                <span class="label--promo__icon label--promo__icon-plus js-promo-add"></span>
                                <p class="label--promo-desc">Убрать промокод</p>
                                <p class="label--promo-desc-error">Промокод истек</p>
                            </label>-->

                            <button data-save-button="true" class="btn btn--green checkout-total__send">Отправить заказ
                            </button>

                            <div class="checkout-total__politic">Нажимая на кнопку, вы соглашаетесь с <a download=""
                                                                                                         href="/politica.pdf">Условиями
                                    обработки
                                    перс. данных</a>, а также с <a download="" href="/oferta.pdf">Условиями продажи</a>
                            </div>

                            <?
                            global $USER;
                            if ($USER->IsAdmin()) {
                                ?>
                                <label class="filter__content-checkbox" for="subscribe_agree" style="margin-top: 20px;">
                                    <input type="checkbox" value="Y" name="subscribe_agree" id="subscribe_agree_msk"
                                           class="form-check-input" checked>
                                    <span style="font-size: 14px;color: #b2b9ce;font-weight: 500;">Подписаться на рассылку</span>
                                </label>
                                <?
                            };
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?
    $signer = new Main\Security\Sign\Signer;
    $signedParams = $signer->sign(base64_encode(serialize($arParams)), 'sale.order.ajax');
    $messages = Loc::loadLanguageFile(__FILE__);
    ?>

	<script>
		// Получаем значение из LocalStorage
		const chosenCity = localStorage.getItem('chosen_city');
	
		// Проверяем значение и показываем соответствующий контейнер
		if (chosenCity === 'Spb') {
			document.getElementById('container-peterburg').style.display = 'block';
		} else {
			document.getElementById('container-moscow').style.display = 'block';
		}
	
		// Убираем проверку полей для скрытого контейнера
		document.addEventListener('DOMContentLoaded', () => {
			const activeContainer = chosenCity === 'Spb' ? 
				document.getElementById('container-peterburg') : 
				document.getElementById('container-moscow');
	
			// Удаляем валидацию или проверки для другого контейнера
			if (activeContainer) {
				const otherContainer = activeContainer.id === 'container-peterburg' ?
					document.getElementById('container-moscow') :
					document.getElementById('container-peterburg');
				
				if (otherContainer) {
					// Очищаем содержимое полей или сбрасываем проверки
					otherContainer.querySelectorAll('input, textarea, select').forEach(field => {
						field.value = '';
						field.removeAttribute('required');
					});
				}
			}
		});
	</script>

    <script>
	
	    function getStyle (elem) {
    
	    return window.getComputedStyle ? getComputedStyle (elem, "") : elem.currentStyle;
        }
	
		var peterburg_container = document.querySelector("div.container.peterburg");
		var moscow_container = document.querySelector("div.container.moscow");
		
		
		/* определение текущего города */
		
		function get_cur_city (ip_address) {
			
		console.log(10);	
			
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
		
		
		
		function set_city_value ( cur_city_value ) {
			
		cur_city_value = JSON.parse(cur_city_value)['location']['data']['city'];
		
		document.querySelector(".msk_city").value = cur_city_value;
		document.querySelector(".spb_city").value = cur_city_value;
		
		var chosen_city_geoposition = cur_city_value;
		var chosen_city_real_geoposition = cur_city_value;
		
		if (chosen_city_geoposition == "Санкт-Петербург") {
			
		chosen_city_geoposition = "Spb";	
		} else {
			
		chosen_city_geoposition = "Moscow";		
		}
		
		var chosen_city_localstorage = localStorage.getItem("chosen_city");
		
		if (!chosen_city_localstorage) {
			
		/* город не выбирал */	
			
		chosen_city_localstorage = chosen_city_geoposition;	
		
		localStorage.setItem("chosen_city", chosen_city_geoposition);
		localStorage.setItem("chosen_real_city", chosen_city_real_geoposition);
		}
	
	    console.log("test");
	
	    if ( localStorage.getItem("chosen_city") == "Spb" ) {
			
		peterburg_container.style.display = "block";	
		moscow_container.style.display = "none";	
		
		var timer_interval = setInterval( function () {
			
		var is_hidden = getStyle( $(".container.peterburg .checkout-block__total .checkout-total--green")[0] ).display;	
			
		if (is_hidden != "none") {
			
		clearInterval( timer_interval );

        setTimeout( function () {
			
		var top_vid_dostavki_price = Number( $(".container.peterburg .checkout-block-item__method-list .checkout-block-item__delivery-wrapper.active")[0].getAttribute("data-price") );
		
		/* var cur_price_dostavka = $(".checkout-total__content .checkout-total--green")[0].textContent;
		var ind = cur_price_dostavka.indexOf(" ");
		cur_price_dostavka = cur_price_dostavka.substring(0, ind); */
		
		$(".container.peterburg .checkout-total__content .checkout-total--green")[0].textContent = top_vid_dostavki_price + " ₽";
		
		var cur_total_price_items = Number( $(".container.peterburg .checkout-total__total")[0].getAttribute("data-price-items") );
		
		cur_total_price_items += top_vid_dostavki_price;
		
		cur_total_price_items = cur_total_price_items + " ₽";
		
		$(".container.peterburg .checkout-total__total")[0].textContent = cur_total_price_items;	
		
		}, 300);
		
		}	
			
		}, 200);
		
		} else {
			
		peterburg_container.style.display = "none";	
		moscow_container.style.display = "block";
		
		var timer_interval = setInterval( function () {
			
		var is_hidden = getStyle( $(".container.moscow .checkout-block__total .checkout-total--green")[0] ).display;	
			
		if (is_hidden != "none") {
			
		clearInterval( timer_interval );		
		
		setTimeout( function () {
		
        var top_vid_dostavki_price = Number( $(".container.moscow .checkout-block-item__method-list .checkout-block-item__delivery-wrapper.active")[0].getAttribute("data-price") );
		
		/* var cur_price_dostavka = $(".checkout-total__content .checkout-total--green")[0].textContent;
		var ind = cur_price_dostavka.indexOf(" ");
		cur_price_dostavka = cur_price_dostavka.substring(0, ind); */
		
		$(".container.moscow .checkout-total__content .checkout-total--green")[0].textContent = top_vid_dostavki_price + " ₽";
		
		var cur_total_price_items = Number( $(".container.moscow .checkout-total__total")[0].getAttribute("data-price-items") );
		
		cur_total_price_items += top_vid_dostavki_price;
		
		cur_total_price_items = cur_total_price_items + " ₽";
		
		$(".container.moscow .checkout-total__total")[0].textContent = cur_total_price_items;
		
		}, 300);
		
		}
		
		}, 200);
		
		}
		
		
		
		
		/* выбор улицы для шаблона спб */
		
		var address_input = document.querySelector(".container.peterburg .spb_address");
		
		$(".container.peterburg #address").autocomplete({
				delay: 500,
				minLength: 2,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var city_input_value = document.querySelector(".container.peterburg input#city").value;
					
					var query = "г " + city_input_value + " " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					var chosen_value = ui['item']['data']['street_with_type'];
					
					address_input.value = chosen_value;
				}
			});
			
			
		/* выбор города для шаблона спб */
			
		var city_input2 = document.querySelector(".container.peterburg .spb_city");	
			
		$(".container.peterburg input#city").autocomplete({
				delay: 500,
				minLength: 1,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var query = "г " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					var chosen_value = ui['item']['data']['city_with_type'];
					
					city_input2.value = chosen_value;
				}
			});		
			
			
		/* выбор дома для шаблона спб */
		
		var house_input = document.querySelector(".container.peterburg .home_spb");
		
		$(".container.peterburg .home_spb").autocomplete({
				delay: 500,
				minLength: 1,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var city_input_value = document.querySelector(".container.peterburg input#city").value;
					var street_input_value = document.querySelector(".container.peterburg .spb_address").value;
					
					var query = "г " + city_input_value + " " + street_input_value + " " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					console.log( ui['item']['data'] );
					
					var chosen_value = ui['item']['value'];
					
					var street_value = $("#address.spb_address")[0].value;
					
					var ind = chosen_value.indexOf(street_value);
					
					if ( ind != (-1) ) {
						
					var house_value = chosen_value.substring(ind + street_value.length+1);	
					house_value = house_value.trim();
					
                    house_input.value = house_value;					
					}
					
				}
			});	
			
			
		/* выбор улицы для шаблона москвы */
		
		var address_input_msk = document.querySelector(".container.moscow input#address");
		
		$(".container.moscow #address").autocomplete({
				delay: 500,
				minLength: 2,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var city_input_value = document.querySelector(".container.moscow input#city").value;
					
					var query = "г " + city_input_value + " " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					var chosen_value = ui['item']['data']['street_with_type'];
					
					address_input_msk.value = chosen_value;
				}
			});
			
			
		/* выбор города для шаблона москвы */
			
		var city_input_msk = document.querySelector(".container.moscow input#city");	
			
		$(".container.moscow input#city").autocomplete({
				delay: 500,
				minLength: 1,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var query = "г " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					var chosen_value = ui['item']['data']['city_with_type'];
					
					city_input_msk.value = chosen_value;
				}
			});	


        /* выбор дома для шаблона москвы */
		
		var home_input_msk = document.querySelector(".container.moscow input.home_moscow");
		
		$(".container.moscow .home_moscow").autocomplete({
				delay: 500,
				minLength: 1,
				source: function(request, response) {
					
					var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";
					var token = "dfb3e71f9984667dcbea2b702ac83971750adad5";
					
					var city_input_value = document.querySelector(".container.moscow input#city").value;
					var street_input_msk = document.querySelector(".container.moscow input#address").value;
					
					var query = "г " + city_input_value + " " + street_input_msk + " " + request.term;
					
					var options = {
					method: "POST",
					mode: "cors",
					headers: {
					"Content-Type": "application/json",
					"Accept": "application/json",
					"Authorization": "Token " + token
					},
					body: JSON.stringify({query: query})
					}

					fetch(url, options)
					.then( response => response.text() )
					.then( result => response( JSON.parse(result)['suggestions'] ) )
					.catch(error => console.log("error", error));
					
				},
				focus: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
				},
				select: function(event, ui) {
					// prevent autocomplete from updating the textbox
					event.preventDefault();
					
					var chosen_value = ui['item']['value'];
					
					var street_value = address_input_msk.value;
					
					var ind = chosen_value.indexOf(street_value);
					
					if ( ind != (-1) ) {
						
					var house_value = chosen_value.substring(ind + street_value.length+1);	
					house_value = house_value.trim();
					
                    home_input_msk.value = house_value;					
					}
				}
			}); 
			
			
		
		}
	
	    /* var choose_city_button = document.getElementById("choose_city_button");
		
		choose_city_button.onclick = function () {
			
		var buttons_block = document.querySelector("#guess_region .buttons");	
		buttons_block.style.display = "none";
			
		var search_input = document.querySelector("#guess_region .search_input");	
		search_input.style.display = "block";	
		
		var guess_region_div = document.querySelector("#guess_region");
		guess_region_div.className = "choose_city";
		}; */
        			
	
        BX.message(<?=CUtil::PhpToJSObject($messages)?>);
        BX.Sale.OrderAjaxComponent.init({
            result: <?=CUtil::PhpToJSObject($arResult['JS_DATA'])?>,
            locations: <?=CUtil::PhpToJSObject($arResult['LOCATIONS'])?>,
            params: <?=CUtil::PhpToJSObject($arParams)?>,
            signedParamsString: '<?=CUtil::JSEscape($signedParams)?>',
            siteID: '<?=CUtil::JSEscape($component->getSiteId())?>',
            ajaxUrl: '<?=CUtil::JSEscape($component->getPath() . '/ajax.php')?>',
            templateFolder: '<?=CUtil::JSEscape($templateFolder)?>',
            propertyValidation: true,
            showWarnings: true,
            pickUpMap: {
                defaultMapPosition: {
                    lat: 55.76,
                    lon: 37.64,
                    zoom: 7
                },
                secureGeoLocation: false,
                geoLocationMaxTime: 5000,
                minToShowNearestBlock: 3,
                nearestPickUpsToShow: 3
            },
            propertyMap: {
                defaultMapPosition: {
                    lat: 55.76,
                    lon: 37.64,
                    zoom: 7
                }
            },
            orderBlockId: 'bx-soa-order',
            authBlockId: 'bx-soa-auth',
            basketBlockId: 'bx-soa-basket',
            regionBlockId: 'bx-soa-region',
            paySystemBlockId: 'bx-soa-paysystem',
            deliveryBlockId: 'bx-soa-delivery',
            pickUpBlockId: 'bx-soa-pickup',
            propsBlockId: 'bx-soa-properties',
            totalBlockId: 'bx-soa-total'
        });
    </script>
    <script>
        <?
        // spike: for children of cities we place this prompt
        $city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
        ?>
        BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
            'source' => $component->getPath() . '/get.php',
            'cityTypeId' => intval($city['ID']),
            'messages' => array(
                'otherLocation' => '--- ' . Loc::getMessage('SOA_OTHER_LOCATION'),
                'moreInfoLocation' => '--- ' . Loc::getMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                'notFoundPrompt' => '<div class="-bx-popup-special-prompt">' . Loc::getMessage('SOA_LOCATION_NOT_FOUND') . '.<br />' . Loc::getMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
                        '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                        '#ANCHOR_END#' => '</a>'
                    )) . '</div>'
            )
        ))?>);
    </script>
    <?php
}

?>

<script>
	console.log($('input[name="method_payment"]:checked').val());

    // Замените на свой API-ключ
    var token = "4b4e4c6012265a4397eeefc265a420d57252f57b";
    var $city = $("#city");
    var cityname = "Москва";

    // удаляет районы города и всё с 65 уровня
    function removeNonCity(suggestions) {
        return suggestions.filter(function (suggestion) {
            return suggestion.data.fias_level !== "5" && suggestion.data.fias_level !== "65";
        });
    }

    function join(arr /*, separator */) {
        var separator = arguments.length > 1 ? arguments[1] : ", ";
        return arr.filter(function (n) {
            return n
        }).join(separator);
    }

    function cityToString(address) {
        return join([
            join([address.city_type, address.city], " "),
            join([address.settlement_type, address.settlement], " ")
        ]);
    }

    // Ограничиваем область поиска от города до населенного пункта
    $city.suggestions({
        token: token,
        type: "ADDRESS",
        hint: false,
        count: 20,
        geoLocation: false,
        bounds: "city-settlement",
        onSuggestionsFetch: removeNonCity,
        onSelect: function (suggestion) {
            console.log(suggestion);
            cityname = suggestion.data.region;
            var check = 0;

            $(".checkout-block-item__delivery-wrapper").each(function (index, el) {


                if ($(el).data('only-moscow') === 'Y') {
                    if (cityname === 'Москва' || cityname === 'Московская') {
                        if (check === 0) {
                            $(el).find('.checkout-block-item__method-item').trigger('click');
                            check = 1;
                        }
                        $(el).show();
                    } else
                        $(el).hide();
                }

                if ($(el).data('no-moscow') === 'Y') {
                    if (cityname !== 'Москва' && cityname !== 'Московская') {
                        if (check === 0) {
                            $(el).find('.checkout-block-item__method-item').trigger('click');
                            check = 1;
                        }
                        $(el).show();
                    } else {
                        $(el).hide();
                    }
                }
            })
        }
    });

    $('#address').on('keydown', function () {
        $("#address").suggestions({
            token: token,
            type: "ADDRESS",
            constraints: {
                // ограничиваем поиск Москвой
                locations: {region: cityname},
            },
            // в списке подсказок не показываем область
            restrict_value: true,
        });
    })

    $(document).on('change', 'input[name="ADDRESS"]', function (e) {
        e.preventDefault();
        //$('.address-choice_block.active').removeClass('active');
        //$(this).parent().addClass('active');
        let strAddr = $(this).val();
        let arAddr = strAddr.split(',');
        let val = '';
        $('#city').val('').removeClass('ierror');
        $('#address').val('').removeClass('ierror');
        $('#home').val('').removeClass('ierror');
        $('#floor').val('').removeClass('ierror');
        $('#apartment').val('').removeClass('ierror');
        arAddr.forEach(function (el) {
            let check = el.split(' ');
            let pref = check[0];
            if (pref == '') {
                pref = check[1];
            }
            switch (pref) {
                case 'г.':
                case 'г':
                    delete (check[0]);
                    val = check.join(' ');
                    $('#city').val(val);
                    break;
                case 'ул':
                case 'ул.':
                    delete (check[0]);
                    delete (check[1]);
                    val = check.join(' ');
                    $('#address').val(val);
                    break;
                case 'корп':
                case 'корп.':
                case 'корпус':
                case 'дом':
                case 'дом.':
                case 'стр':
                case 'стр.':
                case 'строение':
                case 'д':
                case 'к':
                    delete (check[0]);
                    delete (check[1]);
                    val = check.join('');
                    $('#home').val(val);
                    break;
                case 'п.':
                case 'подъезд':
                case 'под':
                case 'под.':
                    delete (check[0]);
                    delete (check[1]);
                    val = check.join('');
                    $('#podezd').val(val);
                    break;
                case 'эт.':
                case 'этаж':
                    delete (check[0]);
                    delete (check[1]);
                    val = check.join('');
                    $('#floor').val(val);
                    break;
                case 'квартира':
                case 'кв':
                case 'кв.':
                    delete (check[0]);
                    delete (check[1]);
                    val = check.join('');
                    $('#apartment').val(val);
                    break;
            }
        })
    })


    $(".checkout-block-item__delivery-wrapper").on("click", function () {
        var name, price;
        name = $(this).data('name');
        price = $(this).data('price');


        console.log(name + ' ' + price);
        $(".checkout-total__delivery-type").html(name);

        if (price) {
            $(".checkout-total--green").html(price + ' ₽');
            var totalPrice = $(".checkout-total__total").data('price');
            totalPrice += price;
            //$(".checkout-total__total").data("price", totalPrice);
            $.post('/local/ajax/currency.php', {price: totalPrice}, function (data) {
                $(".checkout-total__total").html(data);
            });
        } else {
            var price = $(".checkout-total__total").data('price-items');
            $.post('/local/ajax/currency.php', {price: price}, function (data) {
                $(".checkout-total__total").html(data);
                $(".checkout-total__total").data('price', price);
            });
            $(".checkout-total--green").html('бесплатно');
        }
    });
	
	
	function get_real_values () {
		
		/* какой шаблон открыт? */
		
		function getStyle (elem) {
    
	    return window.getComputedStyle ? getComputedStyle (elem, "") : elem.currentStyle;
        }
			
		
		var template_mode;
		
		var container_spb = $(".container.peterburg")[0];
		var container_spb_display = getStyle(container_spb).display;
		
		var container_moscow = $(".container.moscow")[0];
		var container_moscow_display = getStyle(container_moscow).display;
		
		if (container_spb_display != "none") {
			
		template_mode = "template_spb";	
		}
		
		if (container_moscow_display != "none") {
			
		template_mode = "template_msk";	
		}	
		
		if (template_mode == "template_spb") {
		
        city = $('.spb_city')[0].value;
        address = $('.spb_address')[0].value;
        home = $('.home_spb')[0].value;
        floor = $('.floor_spb')[0].value;
        apartment = $('.apartment_spb')[0].value;
        name = $('.name_spb')[0].value;
		surname = $('.surname_spb')[0].value;
        phone = $('.phone_spb')[0].value;
        email = $('.email_spb')[0].value;
		comment = $('.comment_spb')[0].value;
		podezd = $('.podezd_spb')[0].value;
		domofon = $('.domofon_spb')[0].value;
		
		} else {
			
		city = $('.msk_city')[0].value;
        address = $('.msk_address')[0].value;
        home = $('.home_msk')[0].value;
        floor = $('.floor_msk')[0].value;
        apartment = $('.apartment_msk')[0].value;
        name = $('.name_msk')[0].value;
		surname = $('.surname_msk')[0].value;
        phone = $('.phone_msk')[0].value;
        email = $('.email_msk')[0].value;	
		comment = $('.comment_msk')[0].value;
		podezd = $('.podezd_msk')[0].value;
		domofon = $('.domofon_msk')[0].value;
		
		if (email.length == 2) {
			
		email = email[1].value;	
		}
		
		}
		
		var arr_return = [];
		
		arr_return.push(city);
		arr_return.push(address);
		arr_return.push(home);
		arr_return.push(floor);
		arr_return.push(apartment);
		arr_return.push(name);
		arr_return.push(surname);
		arr_return.push(phone);
		arr_return.push(email);
		arr_return.push(comment);
		arr_return.push(podezd);
		arr_return.push(domofon);
		
		return arr_return;
		
	    };

    $(".checkout-total__send").on('click', function (e) {
        e.preventDefault();
		
        let city;
        let address;
        let home;
        let floor;
        let apartment;
        let name;
		let surname;
        let phone;
        let email;
		let comment;
		let podezd;
		let domofon;
		
		let deliveryId;
		let paymentId;
		let subscribeId = "";

        const button = $(this);

        button.addClass('checkout-total__send--disabled');
        button.text('Отправка заказа...');
		
		
		function getStyle (elem) {
    
	    return window.getComputedStyle ? getComputedStyle (elem, "") : elem.currentStyle;
        }
			
		
		var template_mode;
		
		var container_spb = $(".container.peterburg")[0];
		var container_spb_display = getStyle(container_spb).display;
		
		var container_moscow = $(".container.moscow")[0];
		var container_moscow_display = getStyle(container_moscow).display;
		
		if (container_spb_display != "none") {
			
		template_mode = "template_spb";	
		}
		
		if (container_moscow_display != "none") {
			
		template_mode = "template_msk";	
		}
		
		/* if (template_mode == "template_msk") {
			
		deliveryId = $('input[name="method_delivery_msk"]:checked').val();
        console.log('Delivery: ' + deliveryId);	
		
		paymentId = $('input[name="method_payment_msk"]:checked').val();
        console.log('Payment: ' + paymentId);

        subscribeId = $('input[name="subscribe_agree_msk"]:checked').val();
        console.log('Subscribe: ' + subscribeId);	
        subscribeId = "";
		
		} else {
			
		deliveryId = $('input[name="method_delivery"]:checked').val();
        console.log('Delivery: ' + deliveryId);	
		
		deliveryId = 8;
		
		paymentId = $('input[name="method_payment"]:checked').val();
        console.log('Payment: ' + paymentId);
		
		subscribeId = $('input[name="subscribe_agree"]:checked').val();
        console.log('Subscribe: ' + subscribeId);
		subscribeId = "";
		} */

        $(".content-errors").empty();
        $(".content-errors").hide();
		
		var returned_arr = get_real_values ();
		
		city = returned_arr[0];
		address = returned_arr[1];
		home = returned_arr[2];
		floor = returned_arr[3];
		apartment = returned_arr[4];
		name = returned_arr[5];
		surname = returned_arr[6];
		phone = returned_arr[7];
		email = returned_arr[8];
		comment = returned_arr[9];
		podezd = returned_arr[10];
		domofon = returned_arr[11];
		
        let addrValidated = true;
		
        if (deliveryId !== '3' && deliveryId !== '7') {
            if (city.length < 1) {
                addrValidated = false;
                $('#city').addClass('ierror');
            } else {
                $('#city').removeClass('ierror');
            }
            if (address.length < 1) {
                addrValidated = false;
                $('#address').addClass('ierror');
            } else {
                $('#address').removeClass('ierror');

            }
            if (home.length < 1) {
                addrValidated = false;
                $('#home').addClass('ierror');
            } else {
                $('#home').removeClass('ierror');
            }
            if (floor.length < 1) {
                addrValidated = false;
                $('#floor').addClass('ierror');
            } else {
                $('#floor').removeClass('ierror');
            }
            if (apartment.length < 1) {
                addrValidated = false;
                $('#apartment').addClass('ierror');
            } else {
                $('#apartment').removeClass('ierror');
            }
            if (name.length < 1) {
                addrValidated = false;
                $('#name').addClass('ierror');
            } else {
                $('#name').removeClass('ierror');
            }
            if (phone.length < 16) {
                addrValidated = false;
                $('#phone').addClass('ierror');
            } else {
                $('#phone').removeClass('ierror');
            }
            if (email.length < 16) {
                addrValidated = false;
                $('#email').addClass('ierror');
            } else {
                $('#email').removeClass('ierror');
            }
            if (!addrValidated) {
                $("html, body").animate({scrollTop: 0}, 1000);
                $(this).removeClass('checkout-total__send--disabled');
                $(this).text('Отправить заказ');
            }
        };
        // console.log(addrValidated);
        // if (addrValidated) {
        $.post('/local/ajax/create_order.php', {
            FIRST_NAME: name,
            LAST_NAME: surname,
            PHONE: phone,
            ORDER_ID_EMAIL: email,
            COMMENT: comment,
            DELIVERY_ID: $('input[name="method_delivery"]:checked').val(),
            PAYMENT_ID: $('input[name="method_payment"]:checked').val(),
            ORDER_ID_CITY: city,
            ORDER_ID_STREET: address,
            ORDER_ID_HOME: home,
            ORDER_ID_APARTMENT: apartment,
            ORDER_ID_FLOOR: floor,
            ORDER_ID_PODEZD: podezd,
            ORDER_ID_DOORPHONE: domofon,
            SUBSCRIBE: $('input[name="subscribe_agree"]:checked').val(),
        }, function (data) {
            data = JSON.parse(data);
            if (data.status === 'error') {
                $.each(data.errors, function (index, value) {
                    $(".content-errors").show();
                    $(".content-errors").append("<p>" + value + "</p>");
                });
            } else if (data.status === 'success') {
                button.removeClass('checkout-total__send--disabled');
                // button.text('Повторить заказ');
                window.location.href = "/order/success.php"
            }
        });
        // }
    });

    $(document).ready(function () {
        var firstDelivery = $(".checkout-block-item__delivery-wrapper")[0];
        $(firstDelivery).trigger('click');
    });

    // маска

    document.addEventListener('DOMContentLoaded', () => {
        const phoneMaskInputs = document.querySelectorAll('.js-phone-mask');
        phoneMaskInputs.forEach((phoneMaskInput) => {
            IMask(
                phoneMaskInput,
                {
                    mask: '+{7}(000)000-00-00'
                }
            );
        });
    });

$('input[name="method_payment"]').on('click', function () {
    $('input[name="method_payment"]').prop('checked', false); // Сброс
    $(this).prop('checked', true); // Установить checked на выбранный элемент
    console.log('Selected payment method:', $(this).val()); // Проверка значения
});



</script>