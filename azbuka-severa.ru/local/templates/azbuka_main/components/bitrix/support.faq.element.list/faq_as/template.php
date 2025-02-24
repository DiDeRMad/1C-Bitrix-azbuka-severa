<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?foreach ($arResult['ITEMS'] as $key=>$val):?>
<?
	$this->AddEditAction($val['ID'],$val['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($val['ID'],$val['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BSFE_ELEMENT_DELETE_CONFIRM')));
?>
<div class="accordion-faq">
  <div class="top">
    <div class="text"><?=$val['NAME']?></div>
    <img src="<?=$templateFolder?>/images/icon-up.svg" alt="">
    <input type="checkbox">
  </div>
  <div class="bottom">
    <div class="text">
      <?=$val['PREVIEW_TEXT']?>
      <?=$val['DETAIL_TEXT']?>
    </div>
  </div>
</div>
<br/>
<?endforeach;?>