<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
  die();
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
if (empty($arResult['SECTIONS']))
  return false;
?>
<div class="extra-category__special-list">
  <div class="container">
    <h2 class="caption--h2">Другие категории</h2>
    <div class="extra-category__special-list-wrapper">
      <div class="extra-category__special-list-wrapper">
        <?php
        foreach ($arResult['SECTIONS'] as $arSection):
          ?>
          <?php if ($arSection['ELEMENT_CNT'] <= 0)
            continue ?>

            <div class="extra-category__item">
            <div class="extra-category__decor">
              <a href="<?= $arSection['SECTION_PAGE_URL'] ?>">
                <picture>
                  <img src="<?= $arSection['PICTURE']['SRC'] ?>" alt="<?= $arSection['NAME'] ?>">
                </picture>
              </a>
            </div>
            <a href="<?= $arSection['SECTION_PAGE_URL'] ?>" class="extra-category__caption"><?= $arSection['NAME'] ?></a>
            <a href="<?= $arSection['SECTION_PAGE_URL'] ?>"
              class="btn btn--outline btn--outline-carret"><?= num_word($arSection['ELEMENT_CNT'], ['товар', 'товара', 'товаров']) ?>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>