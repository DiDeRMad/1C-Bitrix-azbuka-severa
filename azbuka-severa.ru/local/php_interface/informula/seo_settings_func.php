<?php
\Bitrix\Main\EventManager::getInstance()->addEventHandler( "iblock", "OnTemplateGetFunctionClass", array("InformulaIblockSeoTemplate", "eventHandler"));

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/lib/template/functions/fabric.php");

class InformulaIblockSeoTemplate extends Bitrix\Iblock\Template\Functions\FunctionBase
{
    public static function eventHandler($event) {
        $parameters = $event->getParameters();
        $functionClass = $parameters[0];
        if (is_string($functionClass)) {
            if ($functionClass === "inf_int")
            {
                return new \Bitrix\Main\EventResult(
                    \Bitrix\Main\EventResult::SUCCESS,
                    "\\InformulaIblockSeoTemplate"
                );
            }
        }
    }

    public function calculate($parameters)
    {
        $result = $this->parametersToArray($parameters);
        $asInt = array();
        foreach ($result as $key => $value)
        {
            if (!isset($asInt[$value]))
            {
                $intValue = round($value, 0);
                $asInt[$value] = $intValue;
            }
        }
      
        return $asInt;
    }
}