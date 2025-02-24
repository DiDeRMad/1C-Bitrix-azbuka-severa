<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
//printer($arResult['SECTIONS']);
if (empty($arResult['SECTIONS'])) return false;
?>
<div class="filter__item">
    <div class="filter__name">
        <div class="filter__label">Категории</div>
    </div>
    <div class="filter__content">
        <?foreach ($arResult['SECTIONS'] as $arSection):?>
            <?php if ($arSection['ELEMENT_CNT'] <= 0) continue ?>
            <? 
                $rsResult = CIBlockSection::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => $arSection["IBLOCK_ID"], "ID" => $arSection["ID"]), false, $arSelect = array("UF_*"));
                if($arSection = $rsResult->GetNext()) {  
                    $hide = $arSection["UF_SHOWMENU"];                        
                }                   
            if($hide != 1) {
            ?>
            <a href="<?= $arSection['SECTION_PAGE_URL']?>" class="filter__content-link"><?= $arSection['NAME']?></a>
            <? } ?>
        <?endforeach;?>
    </div>
</div>
