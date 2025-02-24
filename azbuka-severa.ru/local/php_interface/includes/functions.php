<?php
require_once(__DIR__ . '/controllers/WebpConverter.php');

if (!function_exists('dump')) {
	function dump($var, $die = false, $all = false, $export = false)
	{
		global $USER;
		if ($USER->isAdmin() || $all) {
			$bt = debug_backtrace();
			$caller = array_shift($bt);

			print '<pre>';
			print 'File: ' . $caller['file'] . "\n";
			print 'Line: ' . $caller['line'] . "\n";

			if ($export) {
				var_export($var);
			} else {
				// var_dump($var);
				print_r($var);
			}

			print '</pre>';
			if ($die) {
				die();
			}
		}
	}
}

function printer($str)
{
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}

/**
 * выводит массив для отладки
 * @param $what - что выводить
 * @param bool $die - прибивать скрипт?
 * @return вывод
 * @author warenikov <warenik.work@gmail.com>
 */
if (!function_exists('pre')) {
	function pre($what, $die = false)
	{
		global $USER;

		if ($USER->IsAdmin()) {
			echo '<pre>';
			print_r($what);
			echo '</pre>';

			if ($die) die();
		}
	}
}

/**
 * выводит массив для отладки если в консоль браузера
 * @param $data - что выводить
 * @return вывод
 * @author warenikov <warenik.work@gmail.com>
 */
if (!function_exists('log_to_console')) {
	function log_to_console($data, bool $quotes = false)
	{
		$output = json_encode($data);
		if ($quotes) {
			echo "<script>console.log('{$output}' );</script>";
		} else {
			echo "<script>console.log({$output} );</script>";
		}
	}
}


/**
 * Склонение существительных после числительных.
 *
 * @param string $value Значение
 * @param array $words Массив вариантов, например: array('товар', 'товара', 'товаров')
 * @param bool $show Включает значение $value в результирующею строку
 * @return string
 */
function num_word($value, $words, $show = true)
{
	$num = $value % 100;
	if ($num > 19) {
		$num = $num % 10;
	}

	$out = ($show) ? $value . ' ' : '';
	switch ($num) {
		case 1:
			$out .= $words[0];
			break;
		case 2:
		case 3:
		case 4:
			$out .= $words[1];
			break;
		default:
			$out .= $words[2];
			break;
	}

	return $out;
}

if (!function_exists('webpExtension')) {
	function webpExtension($targetImage, $quality = 75)
	{
		if (!$targetImage) {
			return;
		}

		$convertedImage = str_replace(['.jpg', '.jpeg', '.png', '.JPG'], ['.webp', '.webp', '.webp', '.webp'], $targetImage);
		$fullPath = $_SERVER['DOCUMENT_ROOT'] . $convertedImage;

		if (file_exists($fullPath) && filesize($fullPath) > 0) {
			return $convertedImage;
		} else {
			WebpConverter::convert($_SERVER['DOCUMENT_ROOT'] . $targetImage, $quality);
			if (file_exists($fullPath)) {
				return $convertedImage;
			} else {
				return $targetImage;
			}
		}
	}
}

function sortTypesAsc($a, $b)
{
	$param = 'SORT';
	return ($a[$param] > $b[$param]);
}

/**
 * Ф-ция делает первый символ строки с заглавной буквы
 */
if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($string, $enc = 'UTF-8')
	{
		return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) .
			mb_substr($string, 1, mb_strlen($string, $enc), $enc);
	}
}
if (!function_exists('addDiscount')) {
	CModule::IncludeModule('sale');
	function addDiscount($arFields, $discountValue)
	{
		$arDiscountFields = [
			"LID" => 's1',
			"SITE_ID" => 's1',
			"NAME" => "Скидка " . $discountValue . "%" . ' - ' . $arFields['NAME'],
			"DISCOUNT_VALUE" => $discountValue,
			"DISCOUNT_TYPE" => "P",
			"LAST_LEVEL_DISCOUNT" => "N",
			"LAST_DISCOUNT" => "N",
			"ACTIVE" => "Y",
			"CURRENCY" => "RUB",
			"USER_GROUPS" => [1, 2, 3, 4, 5, 6, 7],
			'ACTIONS' => [
				"CLASS_ID" => "CondGroup",
				"DATA" => [
					"All" => "AND"
				],
				"CHILDREN" => [
					[
						"CLASS_ID" => "ActSaleBsktGrp",
						"DATA" => [
							"Type" => "Discount",
							"Value" => $discountValue,
							"Unit" => "Perc",
							"Max" => 0,
							"All" => "AND",
							"True" => "True",
						],
						"CHILDREN" => [
							[
								"CLASS_ID" => "CondBsktAppliedDiscount",
								"DATA" => [
									"value" => "N",
								],
							],
							[
								"CLASS_ID" => "CondIBElement",
								"DATA" => [
									"logic" => "Equal",
									"value" => $arFields['ID'],
								],
							]
						]
					]
				]
			],
			"CONDITIONS" => [
				'CLASS_ID' => 'CondGroup',
				'DATA' => [
					'All' => 'AND',
					'True' => 'True',
				],
				'CHILDREN' => [
					[
						"CLASS_ID" => "CondBsktProductGroup",
						"DATA" => [
							"Found" => "Found",
							"All" => "AND",
						],
						"CHILDREN" => [
							[
								"CLASS_ID" => "CondIBElement",
								"DATA" => [
									"logic" => "Equal",
									"value" => $arFields['ID'],
								]
							]
						]
					],
				],
			]
		];
		CSaleDiscount::Add($arDiscountFields);
	}
}

if (!function_exists("cleanRequest")) {
	// обрабатываем $_GET и $_POST
	function cleanRequest($value)
	{
		if (is_array($value)) {
			foreach ($value as $key => &$sub_value) {
				if ($key == "table") {
				} else {
					$sub_value = cleanRequest($sub_value);
				}
			}
			unset($sub_value);
		} else {
			$value = trim($value);
			$value = stripslashes($value);
			$value = strip_tags($value);
			$value = htmlspecialchars($value, ENT_NOQUOTES);
		}
		return $value;
	}
}


function geoIP()
{
	$ipAddress = $_SERVER['REMOTE_ADDR'];

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

	$result = [
		'region' => $region,
		'city' => $city
	];

	return $result;
}

function setupGeo()
{
	global $thisDomain;
	global $APPLICATION;

	CModule::IncludeModule('iblock');

	$GLOBALS['DOMAINPATH'] = explode('.', $_SERVER['HTTP_HOST']);

	$domain = $_SERVER["SERVER_NAME"];
	if ($domain == "azbuka-severa.ru" || $domain == "www.azbuka-severa.ru") {
		$thisDomain = "main";
	} else {
		$thisDomain = str_replace(".azbuka-severa.ru", "", $domain);
	}
	$APPLICATION->set_cookie("USER_DOMAIN", $thisDomain);
	$GLOBALS['thisDomain'] = $thisDomain ?? 'main';
	if ($thisDomain != '') {
		$subdomains = \Bitrix\Iblock\Elements\ElementSubdomainsTable::getList([
			'order' => ['SORT' => 'ASC', 'NAME' => 'ASC'],
			'select' => ['ID', 'NAME', 'CODE', 'SORT'],
			'filter' => ['=ACTIVE' => 'Y', 'CODE' => $thisDomain],
			"cache" => ["ttl" => 3600],
		])->fetchCollection();
		foreach ($subdomains as $subdomain) {
			$thisDomainId = $subdomain['ID'];
		}
		$GLOBALS['thisDomainId'] = $thisDomainId;
	}
}

function isMobile()
{
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
