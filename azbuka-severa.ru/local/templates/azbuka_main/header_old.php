<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
use Bitrix\Main\Page\Asset;
use Bitrix\Sale;
CModule::IncludeModule('informula.subdomain');
use Informula\Subdomain\Count\CountController;
setupGeo();

/*
if ($GLOBALS["thisDomain"] !== 'main') {
    $GLOBALS['productCounts'] = CountController::getList($GLOBALS['thisDomainId']);
} else {
    unset($GLOBALS['productCounts']);
}
*/

global $APPLICATION;
CModule::IncludeModule('sale');

if (stripos($APPLICATION->GetCurPage(), '/personal/') !== false) {
    LocalRedirect('/catalog/');
}
if (isset($_GET['logout']) && $_GET['logout'] == "yes") {
    LocalRedirect("/");
}
$dirOfThisPage = $APPLICATION->GetCurDir();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <?php
    $APPLICATION->ShowHead();
    if ($overwriteSeoTitle) {
        ?> <title><?=$overwriteSeoTitle?></title> <?
    }
    else {
    ?>
        <title><?$APPLICATION->ShowTitle()?></title>
    <?php } ?>
    <!--<meta charset="UTF-8">-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <?php
    if (!empty($APPLICATION->GetCurDir())){
        $newCanonicalStr = strstr($APPLICATION->GetCurDir(),"filter/",true);
        if ($newCanonicalStr){
            $canonical = 'https://'.$_SERVER['SERVER_NAME'].$newCanonicalStr;
        }
        else{
            //$canonical = 'https://'.$_SERVER['SERVER_NAME'].$APPLICATION->GetCurDir();
        }
    }

    if ($dirOfThisPage === '/') {
        ?> <meta name="robots" content="index, follow" /> <?
    }
    else if (preg_match("/\/catalog\//mi", $dirOfThisPage)) {
        ?> <meta name="robots" content="index, follow" /> <?
    }

    ?>

    <?if($canonical):?>
        <link rel="canonical" href="<?=$canonical; ?>" />
    <?endif?>

    <?if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false && (stripos($APPLICATION->GetCurPage(), '/order/') !== false  || strpos($APPLICATION->GetCurPage(), '/lk/') !== false)):?>
        <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/js/jquery.suggestions.min.js"></script>
    <?endif?>
    <?
        if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false && (stripos($APPLICATION->GetCurPage(), '/order/') !== false || stripos($APPLICATION->GetCurPage(), '/lk/') !== false)) {
            Asset::getInstance()->addString('<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@21.6.0/dist/css/suggestions.min.css" rel="stylesheet" />');
        }
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/libs.min.css");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/app.css");
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/forms.css");

    //if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) {
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/libs.min.js");
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/lazy.min.js");
        //}
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/app.js");
        Asset::getInstance()->addString('<link rel="preload" href="'.SITE_TEMPLATE_PATH.'/assets/css/libs.min.css" as="style">');
        Asset::getInstance()->addString('<link rel="preload" href="'.SITE_TEMPLATE_PATH.'/assets/css/app.css" as="style">');
		Asset::getInstance()->addString('<script src="https://unpkg.com/imask"></script>');
        if (stripos($APPLICATION->GetCurPage(), '/lk/') !== false) {
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/grid.min.css");
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap-datepicker.min.css");
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/bootstrap-datepicker.standalone.min.css");
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/lk.css");
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/collapse-block.css");
            Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/product-list.css");

            Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/bootstrap-datepicker.min.js");
            Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/lk.js");
        }

    ?>

    <?
	// Условие для НГ офорлмения. Если true то подключает скрипты, стили и контейнеры и шапку))
	$NY = true;
	if($NY) Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/newyear/snow.css");
    ?>

    <?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) { ?>
        <script src="https://www.google.com/recaptcha/api.js?render=<?=RECAPTHA_SITEKEY?>"></script>
    <?php } ?>
    
    <?php
        $APPLICATION->ShowPanel();
    ?>

    <?
        // Получаем социальный контент

        // Получение телефона
        $phone = \Dev\Helpers\SocialHelper::getDefault("phone");

        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        $price = $basket->getPrice();
        $userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
        $itInDelay = \Dev\Helpers\FavoriteHelper::getItems($_COOKIE['PHPSESSID'] ?: 'A', $userId ?: "")['UF_ITEMS'];

    ?>
    <meta property="og:url" content="<?= 'https://' . $_SERVER['SERVER_NAME'] . $APPLICATION->GetCurPage()?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?strlen($APPLICATION->ShowProperty("og:title")) ? $APPLICATION->ShowProperty("og:title") : $APPLICATION->ShowTitle() ?>">
    <meta property="og:description" content="<?=$APPLICATION->ShowProperty("og:description")?>">
    <meta property="og:image" content="<?=$APPLICATION->ShowProperty("og:image")?>">
<?php  if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) { ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-211637500-1">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-211637500-1');
</script>
<!-- /Global site tag (gtag.js) - Google Analytics -->
<!-- calltouch -->
<script type="text/javascript">
(function(w,d,n,c){w.CalltouchDataObject=n;w[n]=function(){w[n]["callbacks"].push(arguments)};if(!w[n]["callbacks"]){w[n]["callbacks"]=[]}w[n]["loaded"]=false;if(typeof c!=="object"){c=[c]}w[n]["counters"]=c;for(var i=0;i<c.length;i+=1){p(c[i])}function p(cId){var a=d.getElementsByTagName("script")[0],s=d.createElement("script"),i=function(){a.parentNode.insertBefore(s,a)},m=typeof Array.prototype.find === 'function',n=m?"init-min.js":"init.js";s.type="text/javascript";s.async=true;s.src="https://mod.calltouch.ru/"+n+"?id="+cId;if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",i,false)}else{i()}}})(window,document,"ct","101qg5hs");
</script>
<script>
setTimeout(function(){ 
jQuery(document).on("mousedown", 'form[name="ORDER_FORM"] button.checkout-total__send', function() {
    var m = jQuery(this).closest('form');
        var fio = m.find('input[name="ORDER_PROP_1"],input[autocomplete="name"],input#name').val();
        var phone = m.find('input[name="ORDER_PROP_3"],input[autocomplete="tel"],input#phone').val();
        var mail = m.find('input[name="ORDER_PROP_2"],input[autocomplete="email"],input#email').val();
		var payment = m.find('input[name="method_payment"]').is(':checked'); 
    var ct_site_id = '48223';
    var sub = 'ORDER';
    var ct_data = {           
        fio: fio,
        phoneNumber: phone,
        email: mail,
        subject: sub,
        requestUrl: location.href,
        sessionId: window.call_value
    };
    var ct_valid = !!phone && !!fio && payment;
    if (m.find('input.error').length>0) ct_valid = false;
    if (ct_valid){
     console.log(ct_data,ct_valid);
        jQuery.ajax({
          url: 'https://api.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
          dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
}, 1000);
</script>
<script>
jQuery(document).on("click", 'button.captcha-check', function() {
    var m = jQuery(this).closest('.subscribe__form');
        var mail = m.find('input[type="email"]').val();
    var ct_site_id = '48223';
    var sub = 'Подписаться на рассылку';
    var ct_data = {           
        email: mail,
        subject: sub,
        requestUrl: location.href,
        sessionId: window.call_value
    };
    var ct_valid = !!mail ;
    //console.log(ct_data,ct_valid);
    if (ct_valid && window.ct_snd_flag != 1){
		window.ct_snd_flag = 1; setTimeout(function(){ window.ct_snd_flag = 0; }, 20000);
        jQuery.ajax({
          url: 'https://api.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
          dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>
<script>
jQuery(document).on("click", 'button.btn--blue', function() {
    var m = jQuery(this).closest('.bs-double__form');
        var phone = m.find('input[type="tel"]').val();
    var ct_site_id = '48223';
    var sub = 'Прямо из коптильни';
    var ct_data = {           
        phoneNumber: phone,
        subject: sub,
        requestUrl: location.href,
        sessionId: window.call_value
    };
    var ct_valid = !!phone ;
   // console.log(ct_data,ct_valid);
    if (ct_valid && window.ct_snd_flag != 1 && phone.length > 5){
		window.ct_snd_flag = 1; setTimeout(function(){ window.ct_snd_flag = 0; }, 20000);
        jQuery.ajax({
          url: 'https://api.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
          dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>
<script>
jQuery(document).on("click", 'form#message_form button#message_form_send', function() {
    var m = jQuery(this).closest('form');
        var fio = m.find('input[name="CLIENT_NAME"]').val();
        var phone = m.find('input[name="CLIENT_PHONE"]').val();
		var comment = m.find('textarea[name="COMMENT"]').val();
    var ct_site_id = '48223';
    var sub = 'Оставить сообщение';
    var ct_data = {           
        fio: fio,
        phoneNumber: phone,
		comment: comment,
        subject: sub,
        requestUrl: location.href,
        sessionId: window.call_value
    };
    var ct_valid = !!phone && !!fio && window.ct_snd_flag != 1;
    //console.log(ct_data,ct_valid);
    if (ct_valid){
		window.ct_snd_flag = 1; setTimeout(function(){ window.ct_snd_flag = 0; }, 10000);
        jQuery.ajax({
          url: 'https://api.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
          dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>
<script>
jQuery(document).on("click", 'form#callback_form button#callback_form_send', function() {
    var m = jQuery(this).closest('form');
        var fio = m.find('input[name="CLIENT_NAME"]').val();
        var phone = m.find('input[name="CLIENT_PHONE"]').val();
		var comment = m.find('textarea[name="COMMENT"]').val();
    var ct_site_id = '48223';
    var sub = 'Обратный звонок';
    var ct_data = {           
        fio: fio,
        phoneNumber: phone,
		comment: comment,
        subject: sub,
        requestUrl: location.href,
        sessionId: window.call_value
    };
    var ct_valid = !!phone && !!fio && window.ct_snd_flag != 1;
   // console.log(ct_data,ct_valid);
    if (ct_valid){
		window.ct_snd_flag = 1; setTimeout(function(){ window.ct_snd_flag = 0; }, 10000);
        jQuery.ajax({
          url: 'https://api.calltouch.ru/calls-service/RestAPI/requests/'+ct_site_id+'/register/',
          dataType: 'json', type: 'POST', data: ct_data, async: false
        });
    }
});
</script>
<!-- calltouch -->
<?php } ?>
<?/*<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1044951802607207');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1044951802607207&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->*/?>


</head>
<body>

<?if($NY):?>
	<canvas id="snow"></canvas>
<?endif;?>

<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false): ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(95860328, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:false,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/95860328" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<?endif?>

<?php if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false): ?>
    <div class="lds-default__wrapper">
        <div class="lds-default"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
    </div>
<?endif?>

<?if (stripos($APPLICATION->GetCurPage(), '/order/') === false && stripos($APPLICATION->GetCurPage(), '/login/') === false):?>

<header class="header">

<?/*
    <div class="header-info">
        <div class="container">
            <div class="header-info__wrapper">
                <p class="header-info__descr">Время обработки заказов увеличено. Вам обязательно перезвонят. Отправка заказов в регионы не осуществляется с 23 декабря по 2 января.</p>
                <button class="header-info__close" type="button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.76256 5.17678 6.23744 5.17678 6.53033 5.46967L12 10.9393L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L13.0607 12L18.5303 17.4697C18.8232 17.7626 18.8232 18.2374 18.5303 18.5303C18.2374 18.8232 17.7626 18.8232 17.4697 18.5303L12 13.0607L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L10.9393 12L5.46967 6.53033C5.17678 6.23744 5.17678 5.76256 5.46967 5.46967Z" fill="white"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
*/?>
    <div class="header-top">
       <!-- <noindex>
            <div class="marquee-container">
                  <div class="items-wrap">
                    <div class="items marquee">
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                    </div>
                    <div aria-hidden="true" class="items marquee">
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                      <div class="item">В связи с большим количеством заказов, сроки обработки и доставки увеличены</div>
                    </div>
                  </div>
            </div>
        </noindex> -->
        <div class="container">
            <div class="header-top__wrapper header-top__wrapper--mobile">
                <button type="button" class="header-place">
                    <svg class="icon--24">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#place-icon"></use>
                    </svg>
                    <?php if ($USER->IsAdmin()) { ?>
                        <?php include (__DIR__ . '/include/region_suggest.php'); ?>
                    <?php } else { ?>
                        <span class="header-place__city">Москва</span>
                    <?php } ?>
                </button>
                <a href="/" class="header-logo header-logo--mobile">
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#logo"></use>
                    </svg>
                </a>
                <div class="header-contact header-contact--mobile">
                    <svg class="icon--24">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#phone-icon"></use>
                    </svg>
                     <a href="tel:+74951210110" class="header-contact__number">+7(495)121-0110</a>
                    <button class="header-contact__callback" data-modal="true" data-modal-id="#modal-callback" type="button">Обратный звонок</button>
                </div>

                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "top",
                    Array(
                        "ALLOW_MULTI_SELECT" => "N",
                        "CHILD_MENU_TYPE" => "left",
                        "DELAY" => "N",
                        "MAX_LEVEL" => "1",
                        "MENU_CACHE_GET_VARS" => array(""),
                        "MENU_CACHE_TIME" => "360000",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "MENU_THEME" => "site",
                        "ROOT_MENU_TYPE" => "top",
                        "USE_EXT" => "N",
                        "CACHE_SELECTED_ITEMS" => "N",
                        "MENU_CACHE_USE_USERS" => "N",
                    )
                );?>


		<?include("include/header_social.php");?>

                <button type="button" class="header-top__close-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.76256 5.17678 6.23744 5.17678 6.53033 5.46967L12 10.9393L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L13.0607 12L18.5303 17.4697C18.8232 17.7626 18.8232 18.2374 18.5303 18.5303C18.2374 18.8232 17.7626 18.8232 17.4697 18.5303L12 13.0607L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L10.9393 12L5.46967 6.53033C5.17678 6.23744 5.17678 5.76256 5.46967 5.46967Z" fill="#12266B"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="container js-fx-header">
            <div class="header-bottom__wrapper">
                <a href="/" class="header-logo">
                    <?if($NY):?>
                    <img src="<?= SITE_TEMPLATE_PATH?>/newyear/hat.svg" class="ny-hat">
                    <?endif?>
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#logo"></use>
                    </svg>
                </a>

                <button type="button" class="header-bottom__btn btn btn--blue js-category-btn">
                    <svg class="header-bottom__btn--open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 7C3.25 6.58579 3.58579 6.25 4 6.25H20C20.4142 6.25 20.75 6.58579 20.75 7C20.75 7.41421 20.4142 7.75 20 7.75H4C3.58579 7.75 3.25 7.41421 3.25 7ZM3.25 12C3.25 11.5858 3.58579 11.25 4 11.25H20C20.4142 11.25 20.75 11.5858 20.75 12C20.75 12.4142 20.4142 12.75 20 12.75H4C3.58579 12.75 3.25 12.4142 3.25 12ZM4 16.25C3.58579 16.25 3.25 16.5858 3.25 17C3.25 17.4142 3.58579 17.75 4 17.75H20C20.4142 17.75 20.75 17.4142 20.75 17C20.75 16.5858 20.4142 16.25 20 16.25H4Z"></path>
                    </svg>
                    <svg class="header-bottom__btn--close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.76256 5.17678 6.23744 5.17678 6.53033 5.46967L12 10.9393L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L13.0607 12L18.5303 17.4697C18.8232 17.7626 18.8232 18.2374 18.5303 18.5303C18.2374 18.8232 17.7626 18.8232 17.4697 18.5303L12 13.0607L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L10.9393 12L5.46967 6.53033C5.17678 6.23744 5.17678 5.76256 5.46967 5.46967Z" fill="#ffffff"></path>
                    </svg>
                    <span>Каталог</span>
                </button>

                <div class="header-search">
                    <button type="button" class="header-search-mobile__btn"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.75 11C3.75 6.99594 6.99594 3.75 11 3.75C15.0041 3.75 18.25 6.99594 18.25 11C18.25 15.0041 15.0041 18.25 11 18.25C6.99594 18.25 3.75 15.0041 3.75 11ZM11 2.25C6.16751 2.25 2.25 6.16751 2.25 11C2.25 15.8325 6.16751 19.75 11 19.75C13.1462 19.75 15.112 18.9773 16.6342 17.6949L19.4697 20.5303C19.7626 20.8232 20.2374 20.8232 20.5303 20.5303C20.8232 20.2374 20.8232 19.7626 20.5303 19.4697L17.6949 16.6342C18.9773 15.112 19.75 13.1462 19.75 11C19.75 6.16751 15.8325 2.25 11 2.25Z"></path>
                        </svg></button>
                    <?$APPLICATION->IncludeComponent(
	"bitrix:search.title", 
	"search", 
	array(
		"CATEGORY_0" => array(
			0 => "iblock_catalog",
		),
		"CATEGORY_0_TITLE" => "5",
		"CATEGORY_0_iblock_catalog" => array(
			0 => "5",
		),
		"CHECK_DATES" => "N",
		"CONTAINER_ID" => "title-search",
		"INPUT_ID" => "title-search-input",
		"NUM_CATEGORIES" => "1",
		"ORDER" => "date",
		"PAGE" => "#SITE_DIR#search/index.php",
		"SHOW_INPUT" => "Y",
		"SHOW_OTHERS" => "N",
		"TOP_COUNT" => "5",
		"USE_LANGUAGE_GUESS" => "N",
		"COMPONENT_TEMPLATE" => "search"
	),
	false
);?>

                    <div id="search-name" class="header-search-dropdown">

                    </div>
                </div>

                <div class="header-bottom-mobile">
                    <button type="button" class="header-bottom__btn btn btn--blue js-category-mobile-btn">
                        <svg class="header-bottom__btn--open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 7C3.25 6.58579 3.58579 6.25 4 6.25H20C20.4142 6.25 20.75 6.58579 20.75 7C20.75 7.41421 20.4142 7.75 20 7.75H4C3.58579 7.75 3.25 7.41421 3.25 7ZM3.25 12C3.25 11.5858 3.58579 11.25 4 11.25H20C20.4142 11.25 20.75 11.5858 20.75 12C20.75 12.4142 20.4142 12.75 20 12.75H4C3.58579 12.75 3.25 12.4142 3.25 12ZM4 16.25C3.58579 16.25 3.25 16.5858 3.25 17C3.25 17.4142 3.58579 17.75 4 17.75H20C20.4142 17.75 20.75 17.4142 20.75 17C20.75 16.5858 20.4142 16.25 20 16.25H4Z"></path>
                        </svg>
                        <svg class="header-bottom__btn--close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.76256 5.17678 6.23744 5.17678 6.53033 5.46967L12 10.9393L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L13.0607 12L18.5303 17.4697C18.8232 17.7626 18.8232 18.2374 18.5303 18.5303C18.2374 18.8232 17.7626 18.8232 17.4697 18.5303L12 13.0607L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L10.9393 12L5.46967 6.53033C5.17678 6.23744 5.17678 5.76256 5.46967 5.46967Z" fill="#ffffff"></path>
                        </svg>
                        <span>Каталог</span>
                    </button>

                    <div class="header-bottom__actions">
                        <a href="/" class="header-bottom__actions-item header-bottom__home">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.7019 5.35876C10.3836 4.6771 10.8426 4.22033 11.2301 3.92469C11.6002 3.64226 11.8157 3.57843 12 3.57843C12.1843 3.57843 12.3998 3.64226 12.7699 3.92469C13.1574 4.22033 13.6164 4.6771 14.2981 5.35876L16.4697 7.53033L16.7123 7.77297C17.6227 8.68333 17.9207 9.00017 18.0787 9.3816C18.2367 9.76304 18.25 10.1978 18.25 11.4853V15C18.25 16.4354 18.2484 17.4365 18.1469 18.1919C18.0482 18.9257 17.8678 19.3142 17.591 19.591C17.3142 19.8678 16.9257 20.0482 16.1919 20.1469C15.4365 20.2484 14.4354 20.25 13 20.25H11C9.56458 20.25 8.56347 20.2484 7.80812 20.1469C7.07434 20.0482 6.68577 19.8678 6.40901 19.591C6.13225 19.3142 5.9518 18.9257 5.85315 18.1919C5.75159 17.4365 5.75 16.4354 5.75 15V11.4853C5.75 10.1978 5.76328 9.76304 5.92127 9.3816C6.0788 9.00129 6.37558 8.68519 7.27968 7.78098C7.28235 7.77831 7.28502 7.77564 7.28769 7.77297L7.53033 7.53033C7.53049 7.53017 7.53065 7.53001 7.53081 7.52985L9.7019 5.35876ZM6.47015 6.46919L8.64124 4.2981L8.67801 4.26133C9.31331 3.62599 9.84307 3.09619 10.3202 2.73216C10.8238 2.34793 11.3559 2.07843 12 2.07843C12.6441 2.07843 13.1762 2.34793 13.6798 2.73216C14.1569 3.09619 14.6867 3.62598 15.322 4.26132L15.3588 4.2981L17.5296 6.46897C17.5299 6.4692 17.5301 6.46944 17.5303 6.46967L17.773 6.71231L17.7745 6.71384L17.8738 6.81312C17.9389 6.87809 18.0022 6.94139 18.0639 7.00325L22.5303 11.4697C22.8232 11.7626 22.8232 12.2374 22.5303 12.5303C22.2374 12.8232 21.7626 12.8232 21.4697 12.5303L19.7488 10.8095C19.7502 10.9783 19.7501 11.1556 19.75 11.3427V11.4853V15V15.0549C19.75 16.4225 19.75 17.5248 19.6335 18.3918C19.5125 19.2919 19.2536 20.0497 18.6516 20.6517C18.0497 21.2536 17.2919 21.5125 16.3918 21.6335C15.5248 21.75 14.4225 21.75 13.0549 21.75H13H11H10.9451C9.57754 21.75 8.47522 21.75 7.60825 21.6335C6.70814 21.5125 5.95027 21.2536 5.34835 20.6517C4.74643 20.0497 4.48754 19.2919 4.36652 18.3918C4.24996 17.5248 4.24998 16.4225 4.25 15.0549V15V11.4853L4.24995 11.3427C4.24987 11.1556 4.24978 10.9783 4.25116 10.8095L2.53033 12.5303C2.23744 12.8232 1.76256 12.8232 1.46967 12.5303C1.17678 12.2374 1.17678 11.7626 1.46967 11.4697L5.93593 7.00341C5.99765 6.9415 6.06107 6.87815 6.12616 6.81312V6.81311L6.22625 6.71309L6.22703 6.71231L6.46967 6.46967C6.46983 6.46951 6.46999 6.46935 6.47015 6.46919Z" fill="#12266B"></path>
                            </svg>

                            <span>Главная</span>
                        </a>
                        <a href="/wishlist/" data-count="<?= !empty($itInDelay) ? count($itInDelay) : 0?>" class="header-bottom__actions-item header-bottom__favorite <?if (!empty($itInDelay)) echo 'active'?>">
                            <span class="header-bottom__favorite-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4379 6.16285C8.88102 4.57056 6.31903 4.57056 4.76213 6.16285C3.25393 7.70533 3.25393 10.17 4.76213 11.7125L11.8613 18.973C11.9374 19.0508 12.0626 19.0508 12.1387 18.973L19.2379 11.7125C20.7461 10.17 20.7461 7.70533 19.2379 6.16285C17.681 4.57056 15.119 4.57056 13.5621 6.16285L12.5363 7.21202C12.3952 7.35633 12.2018 7.43768 12 7.43768C11.7982 7.43768 11.6049 7.35633 11.4637 7.21202L10.4379 6.16285ZM3.68962 5.11417C5.83492 2.92012 9.36513 2.92012 11.5104 5.11417L12 5.6149L12.4896 5.11417C14.6349 2.92012 18.1651 2.92012 20.3104 5.11417C22.3886 7.23959 22.3886 10.6357 20.3104 12.7611L13.2112 20.0216C12.5467 20.7012 11.4533 20.7012 10.7888 20.0216L3.68962 12.7611C1.61143 10.6357 1.61143 7.23959 3.68962 5.11417Z"></path>
                                </svg>
                            </span>

                            <span>Избранное</span>
                        </a>

                        <?if ($USER->IsAuthorized()):?>
                            <a href="/lk/" class="header-bottom__actions-item header-bottom__login">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.75 7.5C8.75 5.70507 10.2051 4.25 12 4.25C13.7949 4.25 15.25 5.70507 15.25 7.5C15.25 9.29493 13.7949 10.75 12 10.75C10.2051 10.75 8.75 9.29493 8.75 7.5ZM12 2.75C9.37665 2.75 7.25 4.87665 7.25 7.5C7.25 10.1234 9.37665 12.25 12 12.25C14.6234 12.25 16.75 10.1234 16.75 7.5C16.75 4.87665 14.6234 2.75 12 2.75ZM4.75 18.5C4.75 16.7051 6.20507 15.25 8 15.25H16C17.7949 15.25 19.25 16.7051 19.25 18.5C19.25 19.1904 18.6904 19.75 18 19.75H6C5.30964 19.75 4.75 19.1904 4.75 18.5ZM8 13.75C5.37665 13.75 3.25 15.8766 3.25 18.5C3.25 20.0188 4.48122 21.25 6 21.25H18C19.5188 21.25 20.75 20.0188 20.75 18.5C20.75 15.8766 18.6234 13.75 16 13.75H8Z"></path>
                                </svg>

                                <span>Личный кабинет</span>
                            </a>
                        <?else:?>
                            <a href="/login/" class="header-bottom__actions-item header-bottom__login">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.75 7.5C8.75 5.70507 10.2051 4.25 12 4.25C13.7949 4.25 15.25 5.70507 15.25 7.5C15.25 9.29493 13.7949 10.75 12 10.75C10.2051 10.75 8.75 9.29493 8.75 7.5ZM12 2.75C9.37665 2.75 7.25 4.87665 7.25 7.5C7.25 10.1234 9.37665 12.25 12 12.25C14.6234 12.25 16.75 10.1234 16.75 7.5C16.75 4.87665 14.6234 2.75 12 2.75ZM4.75 18.5C4.75 16.7051 6.20507 15.25 8 15.25H16C17.7949 15.25 19.25 16.7051 19.25 18.5C19.25 19.1904 18.6904 19.75 18 19.75H6C5.30964 19.75 4.75 19.1904 4.75 18.5ZM8 13.75C5.37665 13.75 3.25 15.8766 3.25 18.5C3.25 20.0188 4.48122 21.25 6 21.25H18C19.5188 21.25 20.75 20.0188 20.75 18.5C20.75 15.8766 18.6234 13.75 16 13.75H8Z"></path>
                                </svg>

                                <span>Войти</span>
                            </a>
                        <?endif?>


                        <?if (stripos($APPLICATION->GetCurPage(), '/cart/') === false):?>
                        <a href="/cart/" class="header-bottom__actions-item header-bottom__cart <?if ($price > 0) echo 'active'?>">
                            <span class="header-bottom__cart-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.49998 2.75C1.78467 2.75001 1.88363 2.75062 1.96779 2.7575C2.81868 2.82714 3.55687 3.37236 3.87364 4.16516C3.90498 4.24358 3.93463 4.33789 4.01838 4.61007L5.97994 10.9851L5.99316 11.0281C6.29594 12.0122 6.54213 12.8123 6.81597 13.4359C7.10167 14.0864 7.44679 14.6169 7.98784 15.0165C8.52889 15.4161 9.13745 15.59 9.84321 15.6717C10.5197 15.7501 11.3569 15.7501 12.3865 15.7501H12.4314H14.5358H14.581C15.6178 15.7501 16.4607 15.7501 17.1414 15.6709C17.8515 15.5883 18.4635 15.4126 19.0064 15.0087C19.5493 14.6047 19.8934 14.069 20.1766 13.4126C20.4481 12.7834 20.6902 11.9761 20.9881 10.983L21.0011 10.9397L21.1736 10.365L21.1909 10.3072C21.5299 9.17743 21.8081 8.24989 21.918 7.50536C22.0325 6.72982 21.9907 5.9935 21.5182 5.3585C21.0458 4.7235 20.3525 4.4719 19.5768 4.35869C18.8321 4.25 17.8638 4.25003 16.6842 4.25006H16.6239H5.5C5.49234 4.25006 5.48471 4.25018 5.47711 4.2504L5.45205 4.16894L5.44241 4.13762C5.37193 3.90849 5.32359 3.7513 5.26657 3.60859C4.73861 2.28726 3.5083 1.37857 2.09014 1.2625C1.93697 1.24997 1.77251 1.24998 1.53278 1.25L0.999908 1.25006C0.585695 1.25011 0.249949 1.58594 0.25 2.00015C0.250051 2.41437 0.585878 2.75011 1.00009 2.75006L1.49998 2.75ZM7.41361 10.544L5.93855 5.75006H16.6239C17.8788 5.75006 18.7367 5.75197 19.3602 5.84297C19.9654 5.93129 20.1875 6.08276 20.3148 6.2539C20.4421 6.42504 20.5234 6.68131 20.4341 7.28634C20.3421 7.90971 20.0974 8.7319 19.7368 9.93394L19.5644 10.5086C19.2504 11.5554 19.0322 12.2787 18.7993 12.8185C18.5742 13.3402 18.3653 13.616 18.111 13.8052C17.8567 13.9944 17.5325 14.1153 16.9681 14.1809C16.3841 14.2489 15.6287 14.2501 14.5358 14.2501H12.4314C11.346 14.2501 10.596 14.2489 10.0158 14.1817C9.45511 14.1167 9.13249 13.9972 8.87899 13.8099C8.6255 13.6227 8.41633 13.3495 8.18936 12.8327C7.95451 12.298 7.73281 11.5814 7.41361 10.544ZM10 18.75C9.30964 18.75 8.75 19.3096 8.75 20C8.75 20.6904 9.30964 21.25 10 21.25C10.6904 21.25 11.25 20.6904 11.25 20C11.25 19.3096 10.6904 18.75 10 18.75ZM7.25 20C7.25 18.4812 8.48122 17.25 10 17.25C11.5188 17.25 12.75 18.4812 12.75 20C12.75 21.5188 11.5188 22.75 10 22.75C8.48122 22.75 7.25 21.5188 7.25 20ZM18 18.75C17.3096 18.75 16.75 19.3096 16.75 20C16.75 20.6904 17.3096 21.25 18 21.25C18.6904 21.25 19.25 20.6904 19.25 20C19.25 19.3096 18.6904 18.75 18 18.75ZM15.25 20C15.25 18.4812 16.4812 17.25 18 17.25C19.5188 17.25 20.75 18.4812 20.75 20C20.75 21.5188 19.5188 22.75 18 22.75C16.4812 22.75 15.25 21.5188 15.25 20Z"></path>
                                </svg>
                            </span>
                            <span id="smallCartPrice"><?= $price?> ₽</span>
                        </a>
                        <?endif;?>
                    </div>
                </div>

                <div class="header-mobile-right">
                    <a href="tel:+74951210110" class="header-mobile-right__phone">
                        <svg class="icon--24">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#phone-icon"></use>
                        </svg>
                    </a>

                    <button class="header-mobile-right__menu" type="button">
                        <svg class="header-mobile-right__menu--open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 7C3.25 6.58579 3.58579 6.25 4 6.25H20C20.4142 6.25 20.75 6.58579 20.75 7C20.75 7.41421 20.4142 7.75 20 7.75H4C3.58579 7.75 3.25 7.41421 3.25 7ZM3.25 12C3.25 11.5858 3.58579 11.25 4 11.25H20C20.4142 11.25 20.75 11.5858 20.75 12C20.75 12.4142 20.4142 12.75 20 12.75H4C3.58579 12.75 3.25 12.4142 3.25 12ZM4 16.25C3.58579 16.25 3.25 16.5858 3.25 17C3.25 17.4142 3.58579 17.75 4 17.75H20C20.4142 17.75 20.75 17.4142 20.75 17C20.75 16.5858 20.4142 16.25 20 16.25H4Z" fill="#12266B"></path>
                        </svg>
                        <svg class="header-mobile-right__menu--close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.46967 5.46967C5.76256 5.17678 6.23744 5.17678 6.53033 5.46967L12 10.9393L17.4697 5.46967C17.7626 5.17678 18.2374 5.17678 18.5303 5.46967C18.8232 5.76256 18.8232 6.23744 18.5303 6.53033L13.0607 12L18.5303 17.4697C18.8232 17.7626 18.8232 18.2374 18.5303 18.5303C18.2374 18.8232 17.7626 18.8232 17.4697 18.5303L12 13.0607L6.53033 18.5303C6.23744 18.8232 5.76256 18.8232 5.46967 18.5303C5.17678 18.2374 5.17678 17.7626 5.46967 17.4697L10.9393 12L5.46967 6.53033C5.17678 6.23744 5.17678 5.76256 5.46967 5.46967Z" fill="#12266B"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="header-overlay"></div>
    <?php
    $APPLICATION->IncludeComponent(
        "fouro:menu",
        "",
        Array(
            'IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID,
            "CACHE_SELECTED_ITEMS" => "N",
        )
    );
    /*if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/local/components/fouro/menu/templates/.default/static_version_gen.php')) {
        require($_SERVER['DOCUMENT_ROOT'] . '/local/components/fouro/menu/templates/.default/static_version_gen.php');
    } else {
        require($_SERVER['DOCUMENT_ROOT'] . '/local/components/fouro/menu/templates/.default/static_version.php');
    }*/
    ?>
</header>

<?
$class = "";
$curPage = $APPLICATION->GetCurPage();
if (stripos($curPage, 'catalog') !== false) {
    $class = 'catalog-page';
}
?>

<div class="page <?= $class?>">
<?php else:?>
    <div class="special-page">
        <div class="special-header">
            <div class="container">
                <div class="special-header__wrapper">
                    <a href="/" class="header-logo">
                        <svg>
                            <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#logo"></use>
                        </svg>
                    </a>
                    <div class="header-contact">
                        <a href="tel:+74951210110" class="header-contact__number"><svg class="icon--24">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH?>/img/sprite.svg#phone-icon"></use>
                            </svg> +7 (495) 121-01-10</a>

                        <button class="header-contact__callback" data-modal="true" data-modal-id="#modal-callback" type="button">Обратный звонок</button>
                    </div>
                </div>
            </div>

        </div>
<?endif?>

<?if (
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
):?>
   <div class="container">
<?endif?>
