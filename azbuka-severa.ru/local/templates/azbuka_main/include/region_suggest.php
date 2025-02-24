<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Loader;

global $assets;
$assets = Asset::getInstance();

Loader::includeModule('iblock');

$user_domain = Application::getInstance()->getContext()->getRequest()->getCookie("USER_DOMAIN");
global $user_city, $arCities;

$subdomains = \Bitrix\Iblock\Elements\ElementSubdomainsTable::getList([
    'order'  => ['SORT' => 'ASC', 'NAME' => 'ASC'],
    'select' => ['ID', 'NAME', 'CODE', 'SORT'],
    'filter' => ['=ACTIVE' => 'Y'],
    "cache"  => ["ttl" => 3600],
])->fetchCollection();
$arCities[] = 'Москва';
foreach ($subdomains as $subdomain) {
    $arCities[$subdomain->getCode()] = $subdomain->getName();
}
$assets->addCss(SITE_TEMPLATE_PATH . '/assets/css/guess_region.css');
$assets->addJs(SITE_TEMPLATE_PATH . '/assets/js/guess_region.js');
if ($USER->IsAdmin()) {
    //$GLOBALS['thisDomain'] = 'spb';
}
if ($GLOBALS["thisDomain"] == "main" && !$_SESSION['show_region']) { //!$user_domain &&
    $_SESSION['show_region'] = true;
    $user_geo                = geoIP();
    if (empty($user_geo)) {
        $user_geo = array(
            "region" => "Москва",
            "city"   => "Москва"
        );
    };
    $user_city = $user_geo["city"];
    foreach ($arCities as $cityCode => $city) {
        if (trim($city) == trim($user_geo['region'])) {
            $user_domain = $cityCode;
        }
    }
    if (!$user_domain) {
        $user_domain = 'main';
        $user_city = 'Москва';
    } ?>
    <div id="guess_region">
        <div class="show_city">Ваш город – <?= (!empty($user_city) ? $user_city : 'Москва') ?>?</div>
        <div class="buttons">
			<a href="javascript:void(0);" class="yes" data-region="<?= $user_domain ?>">Да</a>
            <a href="javascript:void(0);" class="no">Нет, выбрать другой</a>
        </div>
    </div>
    <?php
} else {
    $user_domain = $GLOBALS["thisDomain"];
}
$user_city = "Москва";
if ($user_domain) {
    $user_city = $arCities[$user_domain];
}
$base_dir  = Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory();
$base_link = "azbuka-severa.ru";
if ($_SERVER["HTTPS"]) {
    $proto = "https://";
} else {
    $proto = "http://";
}
?>
<span class="header-place__city"><a href="javascript:void(0);" class="show_city"><?= (!empty($user_city) ? $user_city : 'Москва') ?></a></span>

<div id="select_region">
    <div class="select_city">
        <div class="item_wrapper">
            <?
            $lastCol   = false;
            $index     = 0;
            $delimiter = ceil(count($arCities) / 3);
            foreach ($arCities as $code => $city) {
                if ($index >= $delimiter && !$lastCol) {
                    echo "</div><div class=\"item_wrapper\">";
                    $delimiter += ceil(count($arCities) / 3);
                    if ($delimiter >= count($arCities)) $lastCol = true;
                }
                $index++;
                ?>
                <div class="item"><a
                            href="<?= $proto ?><? if ($code != "main") echo $code . "."; ?><?= $base_link ?><?= $base_dir ?>?<?= $_SERVER['QUERY_STRING'] ?>"
                            data-region="<?= $code ?>"><?= $city ?></a></div>
            <?
            }
            ?>
        </div>
    </div>
</div>