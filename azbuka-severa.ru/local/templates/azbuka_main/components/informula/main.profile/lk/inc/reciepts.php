<?php if (!empty($arProfileBlock)) { ?>
    <div class="reviews-slider">
        <?php foreach ($arProfileBlock as $arReciept) {
            $image = CFile::GetFileArray($arReciept['DETAIL_PICTURE']);
            $imageWebp = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $image['SRC']);
            $imageResize = CFile::ResizeImageGet($arReciept['DETAIL_PICTURE'],['width' => '900', 'height' => '400'], BX_RESIZE_IMAGE_PROPORTIONAL, false);
            $imageResizeWebp = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $imageResize['src']);?>
        <div class="community__item-wrapper">
            <a class="community__item-del icon-link icon-link_close add-reciept" href="#"  data-reciept="<?= $arReciept['ID'] ?>"></a>
            <a href="/community/recepties/<?= $arReciept['ID'] ?>/" class="community__item">
                <div class="community__item-img">
                    <picture>
                        <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imageWebp) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageResizeWebp)) { ?>
                            <source srcset="<?= $imageResizeWebp ?> 1x, <?= $imageWebp ?> 2x" type="image/webp">
                        <?php } ?>
                        <img srcset="<?= $imageResize['src'] ?> 1x, <?= $image['SRC'] ?> 2x" src="<?= $imageResize['src'] ?>">
                    </picture>
                </div>
                <div class="community__item-content">
                    <p class="community__item-date"><?=  strtolower(FormatDate("d F", MakeTimeStamp($arReciept['DATE_CREATED']))) ?></p>
                    <p class="community__item-title"><?= $arReciept['NAME'] ?></p>
                </div>
            </a>
        </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="form-group">
        <div class="form-text form-text_lg">В списке пока нет ни одного рецепта</div>
    </div>
    <a href="/community/recepties/" class="btn btn--blue">Перейти в раздел рецепты</a>
<?php } ?>
