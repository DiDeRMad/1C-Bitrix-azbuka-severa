<?php
namespace WD\Utilities;

use
	\WD\Utilities\Helper;

$bSkipAdd = true;
require_once(__DIR__.'/emails_install.php');
unset($bSkipAdd);

if(is_array($arEvents)){
	foreach($arEvents as $strEventType => $arEventType){
		$resTemplates = \CEventMessage::getList($by='ID', $order='ASC', ['TYPE_ID' => $strEventType]);
		while($arTemplate = $resTemplates->fetch()){
			\CEventMessage::delete($arTemplate['ID']);
		}
		\CEventType::delete($strEventType);
	}
}
