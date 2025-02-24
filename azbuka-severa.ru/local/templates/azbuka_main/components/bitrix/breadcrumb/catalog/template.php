<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
    return "";

$strReturn = '';

//we can't use $APPLICATION->SetAdditionalCSS() here because we are inside the buffered function GetNavChain()


$strReturn .= '<div id="navigation" class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
$prelast = $itemSize - 2;
for($index = 0; $index < $itemSize; $index++)
{
    $class = '';
    if ($index == $prelast)
        $class = 'breadcrumb-prelast--item';
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
    {
        $strReturn .= '<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumbs__item '.$class.'">
                           <a  href="'.$arResult[$index]["LINK"].'" itemprop="item">
                               <span itemprop="name">'.$title.'</span>
                               <meta itemprop="position" content="' . $index . '" />
                           </a>
                       </div>';
    }
    else
    {
        $strReturn .= '
			 <span class="breadcrumbs__item">'.$title.'</span>';
    }
}

$strReturn .= '</div>';

return $strReturn;
