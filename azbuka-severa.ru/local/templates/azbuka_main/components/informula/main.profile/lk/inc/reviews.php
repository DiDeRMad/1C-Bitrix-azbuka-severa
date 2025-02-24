<?php if (!empty($arProfileBlock)) { ?>
    <div class="reviews-block-about pd0">
        <div class="reviews-slider" id="reviews-ajax">
            <?php foreach ($arProfileBlock as $arReview) { ?>
            <div class="reviews-slider__item">
                <p class="reviews-slider__name"><?= $arReview['NAME'] ?></p>
                <p class="reviews-slider__description"><?= $arReview['PREVIEW_TEXT'] ?> </p>
                <div class="reviews-slider__meta">
                    <p class="reviews-slider__date"><?=  strtolower(FormatDate("d F", MakeTimeStamp($arReview['DATE_CREATED']))) ?></p>
                </div>
                <p data-state="close" class="reviews-slider__name show_all_review">Читать далее</p>
            </div>
         <?php } ?>
        </div>
    </div>
    <?/*<button type="button" class="btn btn--blue" data-modal="true" data-modal-id="#modal-add-review">Добавить отзыв</button>*/?>
<?php } else { ?>
    <div class="form-group">
        <div class="form-text form-text_lg">В списке пока нет ни одного отзыва</div>
    </div>
    <?/*<button type="button" class="btn btn--blue" data-modal="true" data-modal-id="#modal-add-review">Добавить отзыв
    </button>*/?>
<?php } ?>