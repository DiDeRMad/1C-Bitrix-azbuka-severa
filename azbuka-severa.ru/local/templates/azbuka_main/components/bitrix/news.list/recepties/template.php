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

$navParams = array(
  'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
  'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
  'NavNum' => $arResult['NAV_RESULT']->NavNum
);

$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-'.$arParams['TEMPLATE_THEME'] : '';
?>

<div class="blog">
    <div class="container">
        <div class="blog-list">
            <?foreach ($arResult['ITEMS'] as $arItem):?>
                <a href="<?= $arItem['DETAIL_PAGE_URL']?>" class="community__item">
                    <div class="community__item-img">
                        <img srcset="<?=$arItem['PREVIEW_PICTURE']['SRC_1X']?> 1x, <?=$arItem['PREVIEW_PICTURE']['SRC_2X']?> 2x" src="<?=$arItem['PREVIEW_PICTURE']['SRC_1X']?>">
                    </div>
                    <div class="community__item-content">
                        <p class="community__item-date"><?= $arItem['DATE_CREATE']?></p>
                        <p class="community__item-title"><?= $arItem['NAME']?></p>
                    </div>
                </a>
            <?endforeach;?>
        </div>

        <!--<div class="catalog-list-paginator">
            <div class="catalog-list-paginator__counter">Просмотрено 16 из 874</div>
            <div class="catalog-list-paginator__progress-wrapper">
                <div class="catalog-list-paginator__progress"></div>
            </div>
            <button class="btn btn--outline">Загрузить еще</button>
        </div>-->
        <div class="catalog-list-paginator">
          <?php if ($navParams['NavPageNomer'] < $navParams['NavPageCount']): ?>
              <?
              $url = $_SERVER['REQUEST_URI'];
              if ($_SERVER['QUERY_STRING']) {
                  $url .= '&';
              } else {
                  $url .= '?';
              }
              ?>
              <button data-nextpage="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']+1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>"
                      class="btn btn--outline show_more">Загрузить еще
              </button>
          <?php endif ?>
          <??>
          <div class="catalog-list-paginator__pages">
              <nav class="paging">
              <?if(($navParams['NavPageNomer']-1)>0 && ($navParams['NavPageNomer']-1)<$navParams['NavPageCount']){?>
                  <a href="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']-1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>" class="paging__control paging__control--prev">
                      <sv g class="paging__icon" viewBox="0 0 45 32">
                          <path d="M19.782 5.594c0.665-0.59 1.082-1.446 1.082-2.4 0-1.769-1.434-3.203-3.203-3.203-0.815 0-1.559 0.305-2.125 0.806l0.003-0.003-14.448 12.8c-0.666 0.589-1.083 1.446-1.083 2.4s0.418 1.811 1.080 2.397l0.003 0.003 14.448 12.8c0.562 0.499 1.306 0.803 2.122 0.803 1.769 0 3.203-1.434 3.203-3.203 0-0.954-0.417-1.81-1.078-2.397l-0.003-0.003-8.131-7.194h29.962c1.767 0 3.2-1.433 3.2-3.2s-1.433-3.2-3.2-3.2v0h-29.962z"/>
                      </svg>
                  </a>
              <?}?>
              <?if($navParams['NavPageCount']>1){?>
                  <div class="paging__area">
                      <?
                      $pageNum=1;
                      while($pageNum<=$navParams['NavPageCount']){
                          if($pageNum==$navParams['NavPageNomer']){
                              ?>
                              <span class="paging__item paging__item--active"><?= $pageNum?></span>
                              <?
                          }elseif($pageNum<$navParams['NavPageNomer']){
                              ?>
                              <a class="paging__item" href="<?=$APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($pageNum)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum']))?>"><?= $pageNum?></a>
                              <?
                          }elseif($pageNum>$navParams['NavPageNomer']){
                              ?>
                              <a class="paging__item" href="<?=$APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($pageNum)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum']))?>"><?= $pageNum?></a>
                              <?
                          }
                          $pageNum++;
                      }?>


                  </div>
              <?}?>
                  <?if(($navParams['NavPageNomer']+1)>1 && ($navParams['NavPageNomer']+1)<=$navParams['NavPageCount']){?>
                  <a href="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']+1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>" class="paging__control paging__control--next">
                      <sv g class="paging__icon" viewBox="0 0 45 32">
                          <path d="M25.030 26.406c-0.665 0.59-1.082 1.446-1.082 2.4 0 1.769 1.434 3.203 3.203 3.203 0.815 0 1.559-0.305 2.125-0.806l-0.003 0.003 14.448-12.8c0.666-0.589 1.083-1.446 1.083-2.4s-0.418-1.811-1.080-2.397l-0.003-0.003-14.448-12.8c-0.562-0.499-1.306-0.803-2.122-0.803-1.769 0-3.203 1.434-3.203 3.203 0 0.954 0.417 1.81 1.078 2.397l0.003 0.003 8.131 7.194h-29.962c-1.767 0-3.2 1.433-3.2 3.2s1.433 3.2 3.2 3.2v0h29.962z"/>
                      </svg>
                  </a>
                  <?}?>
              </nav>
          </div>
        <??>
      </div>
    </div>
</div>
