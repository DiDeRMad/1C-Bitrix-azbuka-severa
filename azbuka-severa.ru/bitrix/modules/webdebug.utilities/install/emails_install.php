<?php
namespace WD\Utilities;

use
	\WD\Utilities\Helper;

Helper::loadMessages(__FILE__);

$strLang = 'WDU_EMAILS_';

$obEventType = new \CEventType;
$obEventMessage = new \CEventMessage;

$arSitesId = array_keys(Helper::getSitesList());

$arEvents = [
	'WDU_BACKUP' => [
		'DEFAULT' => [
			'HTML' => true,
			'EMAIL_TO' => '#EMAIL_TO#',
		],
	],
	'WDU_ADMIN_AUTH' => [
		'DEFAULT' => [
			'HTML' => true,
			'EMAIL_TO' => '#EMAIL_TO#',
		],
	],
];

if($bSkipAdd !== true){
	foreach($arEvents as $strEventType => $arEventType){
		$strLangType = $strLang.$strEventType.'_';
		$arFilter = ['TYPE_ID' => $strEventType, 'LID' => LANGUAGE_ID];
		if(!\CEventType::getList($arFilter)->fetch()){
			$arEventTypeFields = [
				'LID' => LANGUAGE_ID,
				'EVENT_NAME' => $strEventType,
				'NAME' => Helper::getMessage($strLangType.'NAME'),
				'DESCRIPTION' => trim(Helper::getMessage($strLangType.'DESC')),
			];
			if($obEventType->add($arEventTypeFields)){
				foreach($arEventType as $strTemplateCode => $arTemplate){
					$strLangTemplate = $strLangType.$strTemplateCode.'_';
					$arEventMessageFields = [
						'ACTIVE' => 'Y',
						'LID' => $arSitesId,
						'EMAIL_FROM' => strlen($arTemplate['EMAIL_FROM']) ? $arTemplate['EMAIL_FROM'] : '#DEFAULT_EMAIL_FROM#',
						'EMAIL_TO' => strlen($arTemplate['EMAIL_TO']) ? $arTemplate['EMAIL_TO'] : '#DEFAULT_EMAIL_FROM#',
						'BODY_TYPE' => $arTemplate['HTML'] ? 'html' : 'text',
						'EVENT_NAME' => $strEventType,
						'EMAIL_TO' => $arTemplate['EMAIL_TO'],
						'SUBJECT' => Helper::getMessage($strLangTemplate.'SUBJECT'),
						'MESSAGE' => trim(Helper::getMessage($strLangTemplate.'MESSAGE')),
					];
					$obEventMessage->add($arEventMessageFields);
				}
			}
		}
	}
}
