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
if (empty($arResult['ITEMS']))
    return false;
?>

<div class="reviews-block">
    <div class="container container">
        <h2 class="caption--h2">Отзывы</h2>
        <div class="reviews-slider">
            <?foreach ($arResult['ITEMS'] as $arItem):?>
                <div class="reviews-slider__item">
                    <p class="reviews-slider__name"><?= $arItem['NAME']?></p>
                    <p class="reviews-slider__description"><?= $arItem['PREVIEW_TEXT']?></p>
                    <div class="reviews-slider__meta">
                        <p class="reviews-slider__date"><?= $arItem['DATE_CREATE']?></p>
                    </div>
                    <?if ($arItem['NEED_SHOW_MORE'] == 'Y'):?>
                        <p data-state="close" class="reviews-slider__name show_all_review">Читать далее</p>
                    <?endif;?>
                </div>
            <?endforeach;?>
        </div>
    </div>
</div>


<script>
    $('.show_all_review').on('click', function () {
        const textOpen = 'Читать далее';
        const textClose = 'Скрыть';

        var state = $(this).data('state');
        var textBlock = $(this).parent().find('.reviews-slider__description');

        if (state === 'close') {
            $(this).html(textClose);
            $(this).data('state', 'open')
            textBlock.css('height', '100%');
        } else {
            $(this).html(textOpen);
            $(this).data('state', 'close')
            textBlock.css('height', '94px');
        }

    })
</script>