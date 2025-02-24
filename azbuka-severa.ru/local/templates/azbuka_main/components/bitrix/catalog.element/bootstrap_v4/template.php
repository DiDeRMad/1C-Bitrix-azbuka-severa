<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);
$uid = uniqid();

$lastModified =  gmdate("D, d M Y H:i:s \G\M\T", strtotime($arResult['TIMESTAMP_X']));
$ifModified = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '', 5));

if ($ifModified && $ifModified >= $lastModified) {
    // страница не изменилась, отдача http статуса 304
    header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
    exit;
}
header('Last-Modified: ' . $lastModified); 

//if($_GET["tst"]) { print "test123 <pre>"; print_r($arResult); }
//print $arResult["CATALOG_QUANTITY"];

$youtubeLink = $arResult['PROPERTIES']['YOUTUBE_LINK']['VALUE'] ?: '#';

// Проверяем, содержит ли ссылка "/shorts/"
if (strpos($youtubeLink, '/shorts/') !== false) {
    // Преобразуем ссылку на Shorts в стандартный вид
    $videoId = basename($youtubeLink); // Извлекаем ID видео
    $youtubeLink = "https://www.youtube.com/watch?v=" . $videoId;
}

?>

<div class="product-card" itemscope itemtype="https://schema.org/Product">
    <div class="container container--small">
        <div class="product-card__wrapper">
            <div class="product-card__img">
                <div class="product-slider-small__clip">
                    <div class="product-slider-small">
                        <?php foreach ($arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_1X'] as $id => $arPicture): ?>
                            <div class="product-slider-small__item">
                                <picture>
                                    <?if ($arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_1X_WEBP'][$id] && $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_2X_WEBP'][$id]):?>
                                        <source srcset="<?= $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_1X_WEBP'][$id]?> 1x, <?= $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_2X_WEBP'][$id]?> 2x" type="image/webp">
                                    <?endif;?>
                                    <img class="lazyload" srcset="<?= $arPicture ?> 1x, <?= $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_2X'][$id] ?> 2x"
                                         data-src="<?= $arPicture ?>" src="<?= $arPicture ?>" alt="<?= $arResult['NAME'] ?>">
                                </picture>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>


                <div class="product-slider-general">
                    <?php foreach ($arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X'] as $id => $arPicture): ?>
                        <a href="<?= $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_2X'][$id] ?>"
                           data-fancybox="gallery" class="product-slider-general__item">
                            <picture>
                                <?if ($arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X_WEBP'][$id] && $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_2X_WEBP'][$id]):?>
                                    <source srcset="<?= $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X_WEBP'][$id]?> 1x, <?= $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X_WEBP'][$id]?> 2x" type="image/webp">
                                <?endif?>
                                <img class="lazyload" srcset="<?= $arPicture ?> 1x, <?= $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_2X'][$id] ?> 2x"
                                     data-src="<?= $arPicture ?>" src="<?= $arPicture ?>" alt="<?= $arResult['NAME'] ?>" width="540" height="540">
                            </picture>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="display: none;">
                <img itemprop="image" src="https://azbuka-severa.ru/<?= $arResult['SCHEMA_PICTURE']?>" alt="">
            </div>

            <div id="tobread" class="breadcrumbs">
                <!-- <a href="#!" class="breadcrumbs__item">Главная
                 </a>

                 <a href="#!" class="breadcrumbs__item">Каталог
                 </a>
                 <a href="#!" class="breadcrumbs__item">Морепродукты
                 </a>
                 <span class="breadcrumbs__item">Осьминоги</span>-->
            </div>
            <div class="product-card__content">

                <div class="product-card__title">
                    <h1 itemprop="name"><?= $arResult['NAME'] ?></h1>
                </div>

                <div style="display: none;" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span itemprop="priceCurrency" content="RUB">₽</span><span
                            itemprop="price" content="<?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?: $arResult['MIN_PRICE']['VALUE']?>"><?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?: $arResult['MIN_PRICE']['VALUE']?></span>
                    <link itemprop="availability" href="https://schema.org/<?= $arResult['PRODUCT']['QUANTITY'] > 0 ? 'InStock' : 'OutOfStock'?>" />
                </div>

                <? if (!empty($arResult['OFFERS'])): ?>
                    <div class="product-card__choice">
                        <p class="product-card__choice-title"><?= $arResult['TITLE_OFFER']?> :</p>
                        <div class="product-card__choice-list">

                            <?
                            array_multisort( array_column($arResult['OFFERS'], "WEIGHT"), SORT_NATURAL, SORT_ASC, $arResult['OFFERS'] );

                            ?>
                            <? foreach ($arResult['OFFERS'] as $arOffer): ?>
                            
                            <?if ($APPLICATION->GetCurPage() != $arOffer['LINK']):?>
                                <a href="<?= $arOffer['LINK'] ?>"
                                   class="product-card__choice-item <?= $arOffer['ACTIVE'] ?>"><?= $arOffer['WEIGHT'] ?></a>
                            <?else:?>
                                    <p
                                       class="product-card__choice-item <?= $arOffer['ACTIVE'] ?>"><?= $arOffer['WEIGHT'] ?></p>
                            <?endif?>
                            <? endforeach; ?>
                            <!--                            <a href="#!" class="product-card__choice-item disabled">2-2.5</a>-->
                        </div>
                    </div>
                <? endif; ?>


                <div class="label-quantity">
                    <?if($arResult["CATALOG_QUANTITY"] > 0):?>
                        <span class="available">В наличии</span>
                    <?else:?>
                        Нет в наличии
                    <?endif;?>
                </div>

                <div class="label-container">
                    <?php if ($arResult['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']): ?>
                        <div class="product-card__label-wrapper">
                            <div class="product-card__label">-<?= $arResult['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] ?>%
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if ($arResult['PROPERTIES']['HIT']['VALUE']): ?>
                        <div class="product-card__label-wrapper">
                            <div class="product-card__label">Хит продаж
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-card__price-wrapper">
                    <?php if ($arResult['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']): ?>
                        <div class="product-card__price-current"><?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?> ₽</div>
                        <div class="product-card__price-old"><?= $arResult['MIN_PRICE']['VALUE'] ?> ₽</div>
                        <?php if ($arResult['PROPERTIES']['weight']['VALUE'] > 0): ?>
                            <div class="product-card__options"><?= $arResult['PROPERTIES']['weight']['VALUE'] ?> <?= $arResult['PROPERTIES']['weight']['VALUE_TYPE'] ?></div>
                        <?php endif ?>
                    <?php else: ?>
                        <div class="product-card__price-current"><?= $arResult['MIN_PRICE']['VALUE'] ?> ₽</div>
                        <?php if ($arResult['PROPERTIES']['weight']['VALUE'] > 0): ?>
                            <div class="product-card__options"><?= 'Цена за ' . $arResult['PROPERTIES']['weight']['VALUE'] ?> <?= $arResult['PROPERTIES']['weight']['VALUE_TYPE'] ?></div>
                        <?php endif ?>
                    <?php endif ?>
                    <p style="display: none;" data-id="<?= $arResult['ID']?>" id="gobasket_<?= $arResult['ID'] . '_' . $uid?>" class="go-to-basket" onclick="window.location.href = '/cart/'">В корзине, перейти в корзину</p>

                </div>

                    <div class="product-card__add-info"> <?if ($arResult['PROPERTIES']['measureunit']['VALUE'] != 'шт'):?>Окончательная стоимость будет известна после взвешивания<?endif?><?php if ($arResult['PRODUCT']['QUANTITY'] <= 0):?><br>Окончательная стоимость будет известна после свежего поступления.<?endif?></div>


                <div class="product-card__btn-wrapper">
                    <div data-id="<?= $arResult['ID']?>" class="product-card__control">
                        <?php if ($arResult['PRODUCT']['QUANTITY'] > 0):?>
                        <button data-id="<?= $arResult['ID']?>" onclick="addToBasket(
                        <?= $arResult['ID'] ?>,
                        <?= $arResult['CATALOG_MEASURE_RATIO'] ?>,
                        <?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?>,
                                this)" class="btn btn--blue product-card__buy active" type="button">В корзину
                        </button>

                        <div data-id="<?= $arResult['ID']?>" class="product-counter product-card__counter-block">
                            <button type="button" class="product-counter__btn js-product-minus" onclick="changeQuantity(
                            <?= $arResult['ID']?>,
                            <?= $arResult['CATALOG_MEASURE_RATIO']?>,
                                    'minus'
                                    )"></button>
                            <div class="product-counter__all-in">
                                <!-- ед. измерения и шаг -->
                                <input type="hidden" class="js-product-units" value="<?= $arResult['PROPERTIES']['measureunit']['VALUE']?>">
                                <input type="hidden" class="js-product-step" value="<?= $arResult['CATALOG_MEASURE_RATIO']?>">
                                <!-- ед. измерения и шаг -->
                                <input data-id="<?= $arResult['ID']?>" type="hidden" id="<?= $arParams['CLASS'] . '_'?>basket-quan1_<?= $arResult['ID']?>" class="js-really-quantity" value="1">
                                <input data-id="<?= $arResult['ID']?>" type="text" id="<?= $arParams['CLASS'] . '_'?>basket-quan2_<?= $arResult['ID']?>" class="product-counter__input" data-type="<?= $arResult['PROPERTIES']['measureunit']['VALUE']?>" value="<?= $arResult['QUANTITY_BASKET'] . ' ' . $arResult['PROPERTIES']['measureunit']['VALUE']?>" readonly="">
                            </div>
                            <button type="button" class="product-counter__btn js-product-plus" onclick="changeQuantity(
                            <?= $arResult['ID']?>,
                            <?= $arResult['CATALOG_MEASURE_RATIO']?>,
                                    'plus'
                                    )"></button>
                        </div>
                        <?else:?>
                            <button type="button" data-modal="true" onclick="$('#send-item').val(<?= $arResult['ID']?>)" data-modal-id="#modal-item-buy" class="btn btn--outline-red product-item__btn-order active">Под заказ</button>
                        <?endif?>
                    </div>

                    <button type="button" data-modal="true" onclick="$('#this-product').val(<?= $arResult['ID']?>)" data-modal-id="#modal-one-click" class="btn btn--outline">Купить в 1 клик</button>


                    <button data-id="<?= $arResult['ID']?>" onclick="add2wishDetail(
                            '<?= $arResult["ID"] ?>',
                            this)" id="favorite_detail_<?= $arResult['ID'] ?>"
                            class="btn btn--outline product-card__favorite">
                        <svg class="icon--24">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#wishlist-icon"></use>
                        </svg>
                        В избранное
                    </button>

                </div>

                <div class="product-card__description">
                    <?= $arResult['DETAIL_TEXT'] ?>
                    <?
                    // SEO текст для страницы
                    if (!empty($arResult["SEO_TEXT"])) {
                        echo implode("<br>", $arResult["SEO_TEXT"]);
                    }
                    ?>
                </div>
    
                <div class="product-card__btn-wrapper">

                    <?if ($GLOBALS["USER"]->IsAdmin()):?>
                        <button type="button" class="btn btn--gray" data-modal="true" data-modal-id="#modal-delivery-terms">Доставка и оплата</button>
                    <?endif?>

                    <?php if ($USER->IsAuthorized()) { ?>
                        <button type="button" class="btn btn--outline js-review-add" data-modal="true" data-modal-id="#modal-add-review" data-elem="<?= $arResult['ID'] ?>">Добавить отзыв</button>
                    <?php } ?>
    
                </div>


            </div>
        </div>
    </div>
</div>

<div class="about-product">
    <div class="container container--small">
        <div class="about-product__wrapper">
            <div class="about-product__info">
                <h3 class="about-product__title">О продукте</h3>
                <div class="about-product__meta">
                    
                    <?php foreach ($arResult['PROPERTIES']['ALL_CHARACTERS'] as $arCharacter): ?>
                        <?php if ($arCharacter['RESULT']): ?>
                            <div class="about-product__prop">
                                <div class="about-product__prop-label">
                                    <?= $arCharacter['NAME'] ?>
                                </div>
                                <div class="about-product__prop-value">
                                    <div class="nutritional">
                                        <?php foreach ($arCharacter['RESULT'] as $key => $arOptions): ?>
                                            <? if ($arOptions['VALUE']): ?>
                                                <div class="nutritional__set">
                                                    <div class="nutritional__label">
                                                        <?= $arOptions['TEXT'] ?>
                                                    </div>
                                                    <div class="nutritional__value">
                                                        <?= $arOptions['VALUE'] ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <? if ($arCharacter['VALUE']): ?>
                                <div class="about-product__prop">
                                    <div class="about-product__prop-label">
                                        <?= $arCharacter['NAME'] ?>
                                    </div>
                                    <div class="about-product__prop-value">
                                        <?= $arCharacter['VALUE'] ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endforeach; ?>
                    



                    <?/* old view?>
                        <?php foreach ($arResult['PROPERTIES']['ALL_CHARACTERS'] as $arCharacter): ?>
                            <?php if ($arCharacter['RESULT']): ?>
                                <p class="about-product--row">
                                    <span class="about-product--gray"><?= $arCharacter['NAME'] ?></span>
                                </p>
                                <?php foreach ($arCharacter['RESULT'] as $key => $arOptions): ?>
                                    <? if ($arOptions['VALUE']): ?>
                                        <p class="about-product--row <?php if ($key == count($arCharacter['RESULT']) - 1) echo 'about-product--space' ?>">
                                            <span><?= $arOptions['TEXT'] ?></span>
                                            <span><?= $arOptions['VALUE'] ?></span>
                                        </p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <? if ($arCharacter['VALUE']): ?>
                                    <? if ($arCharacter['CODE'] == 'CHARACTER_STRUCTURE'): ?>
                                        <span class="about-product--gray"><?= $arCharacter['NAME'] ?></span>
                                        <?if ($arCharacter['LONG'] && $arCharacter['SHORT']):?>
                                            <div class="js-short-recipe"
                                                data-long="<?= $arCharacter['LONG'] ?>">
                                                <?= $arCharacter['SHORT'] ?>
                                            </div>
                                        <?else:?>
                                            <div class="js-short-recipe"
                                                <span><?= $arCharacter['VALUE'] ?></span>
                                            </div>
                                        <?endif?>
                                    <? else: ?>
                                        <p class="about-product--row about-product--space">
                                            <span class="about-product--gray"><?= $arCharacter['NAME'] ?></span>
                                            <span><?= $arCharacter['VALUE'] ?></span>
                                        </p>
                                    <? endif; ?>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endforeach; ?>
                    <?*/?>

                </div>

		<div class="weasocial">
			<span class="header-social">Поделиться в социальных сетях:</span>
			<?//include("/home/bitrix/www/local/templates/azbuka_main/include/header_social.php");?>
			<div class="weasocial-icons">
				<script src="https://yastatic.net/share2/share.js"></script>
				<div class="ya-share2" data-curtain data-size="l" data-shape="round" data-services="vkontakte,odnoklassniki,telegram,viber,whatsapp,moimir"></div>
			</div>
		</div>

            </div>
            <? if ($arResult['PROPERTIES']['VIDEO_PHOTO']['VALUE']): ?>
            <?if ($arResult['PROPERTIES']['YOUTUBE_LINK']['VALUE']):?>
                <a href="<?= $youtubeLink ?>" <?if ($arResult['PROPERTIES']['YOUTUBE_LINK']['VALUE']):?> data-fancybox="" <?endif?>
                   class="about-product__img product_player">
                    <picture>
                        <?if ($arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP'] && $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC_WEBP']):?>
                            <source srcset="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP']?> 2x" type="image/webp">
                        <?endif?>
                        <img class="lazyload" srcset="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC'] ?> 2x"
                             data-src="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?>" src="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>">
                    </picture>
                </a>
            <?else:?>
                <div
                   class="about-product__img">
                    <picture>
                        <?if ($arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP'] && $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC_WEBP']):?>
                            <source srcset="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP']?> 2x" type="image/webp">
                        <?endif?>
                        <img class="lazyload" srcset="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC'] ?> 2x"
                             data-src="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?>" src="<?= $arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>">
                    </picture>
                </div>
            <?endif?>
            <? endif; ?>
        </div>
        <?if ($arResult['PREVIEW_TEXT']):?>
            <div class="about-product__description">
                <p>
                    <?= $arResult['PREVIEW_TEXT'] ?>
                </p>
                
                    <div style="margin-top:40px;">
                        <span>Смотрите также:
                        <?php foreach ($arResult['SECTION']['PATH'] as $pathItem) { ?>
                            <a href="<?= $pathItem['SECTION_PAGE_URL'] ?>" style="margin-left:10px; color: #1b389e; font-weight: 600;"><?= strtolower($pathItem['NAME']) ?></a>
                        <?php } ?>
                        </span>
            </div>
        <?endif;?>
    </div>
</div>
<?php if ($arResult['RECEPT']): ?>
    <div class="product-recipe">
        <div class="container container--small">
            <div class="product-recipe__wrapper">
                <h3 class="caption--h2">Вкусный рецепт</h3>

                <div class="product-recipe__item">
                    <div class="product-recipe__img">
                        <img class="lazyload" srcset="<?= $arResult['RECEPT']['PICTURE']['SRC_1X'] ?> 1x, <?= $arResult['RECEPT']['PICTURE']['SRC_2X'] ?> 2x"
                             data-src="<?= $arResult['RECEPT']['PICTURE']['SRC_1X'] ?>">
                    </div>
                    <div class="product-recipe__content">
                        <p class="product-recipe__title"><?= $arResult['RECEPT']['NAME'] ?></p>
                        <div class="product-recipe__description"><?= $arResult['RECEPT']['DETAIL_TEXT'] ?>
                        </div>

                        <? if ($arResult['RECEPT']['TAGS']): ?>
                            <div class="product-recipe__tag">
                                <p class="product-recipe__subtitle">Необходимые ингредиенты</p>
                                <div class="product-recipe__tag-list">
                                    <? foreach ($arResult['RECEPT']['TAGS'] as $arTag): ?>
                                        <a href="<?= $arTag['LINK'] ?>"
                                           class="btn btn--gray product-recipe__tag-item"><?= $arTag['TEXT'] ?></a>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        <? endif; ?>

                        <? if ($arResult['RECEPT']['TIME_TO_COOK']): ?>
                            <div class="product-recipe__time-wrapper">
                                <p class="product-recipe__subtitle">Время приготовления</p>
                                <p class="product-recipe__time">
                                    <svg class="icon--24">
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#time-icon"></use>
                                    </svg>
                                    <?= $arResult['RECEPT']['TIME_TO_COOK'] ?>
                                </p>
                            </div>
                        <? endif; ?>

                        <div class="product-recipe__btn-wrapper">
                            <button onclick="window.location.href = '<?= $arResult['RECEPT']['DETAIL_PAGE_URL'] ?>';"
                                    class="btn btn--outline product-recipe__btn-detail">Подробнее
                            </button>
                            <button onclick="window.location.href = '/community/recepties/';"
                                    class="btn btn--outline product-recipe__btn-more">Больше рецептов
                                <svg class="icon--24">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#caret-right-icon"></use>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
global $reviewFilter;
$reviewFilter['PROPERTY_PRODUCT'] = $arResult['ID'];
?>
<? $APPLICATION->IncludeComponent("bitrix:news.list", "product_review", array(
    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
    "ADD_SECTIONS_CHAIN" => "N",    // Включать раздел в цепочку навигации
    "AJAX_MODE" => "N",    // Включить режим AJAX
    "AJAX_OPTION_ADDITIONAL" => "",    // Дополнительный идентификатор
    "AJAX_OPTION_HISTORY" => "N",    // Включить эмуляцию навигации браузера
    "AJAX_OPTION_JUMP" => "N",    // Включить прокрутку к началу компонента
    "AJAX_OPTION_STYLE" => "N",    // Включить подгрузку стилей
    "CACHE_FILTER" => "Y",    // Кешировать при установленном фильтре
    "CACHE_GROUPS" => "N",    // Учитывать права доступа
    "CACHE_TIME" => "36000000",    // Время кеширования (сек.)
    "CACHE_TYPE" => "A",    // Тип кеширования
    "CHECK_DATES" => "Y",    // Показывать только активные на данный момент элементы
    "DETAIL_URL" => "",    // URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
    "DISPLAY_BOTTOM_PAGER" => "N",    // Выводить под списком
    "DISPLAY_DATE" => "N",    // Выводить дату элемента
    "DISPLAY_NAME" => "Y",    // Выводить название элемента
    "DISPLAY_PICTURE" => "Y",    // Выводить изображение для анонса
    "DISPLAY_PREVIEW_TEXT" => "Y",    // Выводить текст анонса
    "DISPLAY_TOP_PAGER" => "N",    // Выводить над списком
    "FIELD_CODE" => array(    // Поля
        0 => "ID",
        1 => "NAME",
        2 => "PREVIEW_TEXT",
        3 => "PREVIEW_PICTURE",
        4 => "",
    ),
    "FILTER_NAME" => "reviewFilter",    // Фильтр
    "HIDE_LINK_WHEN_NO_DETAIL" => "N",    // Скрывать ссылку, если нет детального описания
    "IBLOCK_ID" => IBLOCK_PRODUCT_REVIEWS_ID,    // Код информационного блока
    "IBLOCK_TYPE" => "main_content",    // Тип информационного блока (используется только для проверки)
    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",    // Включать инфоблок в цепочку навигации
    "INCLUDE_SUBSECTIONS" => "N",    // Показывать элементы подразделов раздела
    "MEDIA_PROPERTY" => "",    // Свойство для отображения медиа
    "MESSAGE_404" => "",    // Сообщение для показа (по умолчанию из компонента)
    "NEWS_COUNT" => "20",    // Количество новостей на странице
    "PAGER_BASE_LINK_ENABLE" => "N",    // Включить обработку ссылок
    "PAGER_DESC_NUMBERING" => "N",    // Использовать обратную навигацию
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",    // Время кеширования страниц для обратной навигации
    "PAGER_SHOW_ALL" => "N",    // Показывать ссылку "Все"
    "PAGER_SHOW_ALWAYS" => "N",    // Выводить всегда
    "PAGER_TEMPLATE" => ".default",    // Шаблон постраничной навигации
    "PAGER_TITLE" => "Новости",    // Название категорий
    "PARENT_SECTION" => "",    // ID раздела
    "PARENT_SECTION_CODE" => "",    // Код раздела
    "PREVIEW_TRUNCATE_LEN" => "",    // Максимальная длина анонса для вывода (только для типа текст)
    "PROPERTY_CODE" => array(    // Свойства
        0 => "LINK",
        1 => "",
    ),
    "SEARCH_PAGE" => "/search/",    // Путь к странице поиска
    "SET_BROWSER_TITLE" => "N",    // Устанавливать заголовок окна браузера
    "SET_LAST_MODIFIED" => "N",    // Устанавливать в заголовках ответа время модификации страницы
    "SET_META_DESCRIPTION" => "N",    // Устанавливать описание страницы
    "SET_META_KEYWORDS" => "N",    // Устанавливать ключевые слова страницы
    "SET_STATUS_404" => "N",    // Устанавливать статус 404
    "SET_TITLE" => "N",    // Устанавливать заголовок страницы
    "SHOW_404" => "N",    // Показ специальной страницы
    "SLIDER_PROPERTY" => "",    // Свойство с изображениями для слайдера
    "SORT_BY1" => "ACTIVE_FROM",    // Поле для первой сортировки новостей
    "SORT_BY2" => "SORT",    // Поле для второй сортировки новостей
    "SORT_ORDER1" => "DESC",    // Направление для первой сортировки новостей
    "SORT_ORDER2" => "ASC",    // Направление для второй сортировки новостей
    "STRICT_SECTION_CHECK" => "N",    // Строгая проверка раздела для показа списка
    "TEMPLATE_THEME" => "blue",    // Цветовая тема
    "USE_RATING" => "N",    // Разрешить голосование
    "USE_SHARE" => "N",    // Отображать панель соц. закладок
),
    false
); ?>
<?php if (!empty($arResult['PROPERTIES']['OTHER_PRODUCTS']['VALUE'])):?>

<?php
global $arrFilter;
$arrFilter['ID'] = $arResult['PROPERTIES']['OTHER_PRODUCTS']['VALUE'];
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "slider",
    array(
        "ACTION_VARIABLE" => "action",
        "ADD_PICT_PROP" => "-",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_TO_BASKET_ACTION" => "ADD",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "BACKGROUND_IMAGE" => "-",
        "BASKET_URL" => "/personal/basket.php",
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "N",
        "CACHE_TIME" => "43200",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "Y",
        "CONVERT_CURRENCY" => "N",
        "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
        "DETAIL_URL" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "arrFilter",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => "5",
        "IBLOCK_TYPE" => "catalog",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LABEL_PROP" => array(),
        "LAZY_LOAD" => "N",
        "LINE_ELEMENT_COUNT" => "3",
        "LOAD_ON_SCROLL" => "N",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "OFFERS_LIMIT" => "5",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "18",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => array("BASE"),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
        "PRODUCT_SUBSCRIPTION" => "Y",
        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
        "RCM_TYPE" => "personal",
        "SECTION_CODE" => "",
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_URL" => "",
        "SECTION_USER_FIELDS" => array("", ""),
        "SEF_MODE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_ALL_WO_SECTION" => "N",
        "SHOW_CLOSE_POPUP" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_FROM_SECTION" => "N",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_SLIDER" => "Y",
        "SLIDER_INTERVAL" => "3000",
        "SLIDER_PROGRESS" => "N",
        "TEMPLATE_THEME" => "blue",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "TITLE" => "Другие продукты",
        "CLASS" => 'other'
    )
); ?>
<?php endif?>

<?php if (!empty($arResult['PROPERTIES']['RECOMMENDED']['VALUE'])):?>
<?php
global $arrFilter;
$arrFilter['ID'] = $arResult['PROPERTIES']['RECOMMENDED']['VALUE'];
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "slider",
    array(
        "ACTION_VARIABLE" => "action",
        "ADD_PICT_PROP" => "-",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_TO_BASKET_ACTION" => "ADD",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "BACKGROUND_IMAGE" => "-",
        "BASKET_URL" => "/personal/basket.php",
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "43200",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "Y",
        "CONVERT_CURRENCY" => "N",
        "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
        "DETAIL_URL" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "arrFilter",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "IBLOCK_ID" => "5",
        "IBLOCK_TYPE" => "catalog",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LABEL_PROP" => array(),
        "LAZY_LOAD" => "N",
        "LINE_ELEMENT_COUNT" => "3",
        "LOAD_ON_SCROLL" => "N",
        "MESSAGE_404" => "",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "META_DESCRIPTION" => "-",
        "META_KEYWORDS" => "-",
        "OFFERS_LIMIT" => "5",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Товары",
        "PAGE_ELEMENT_COUNT" => "18",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => array("BASE"),
        "PRICE_VAT_INCLUDE" => "Y",
        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
        "PRODUCT_SUBSCRIPTION" => "Y",
        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
        "RCM_TYPE" => "personal",
        "SECTION_CODE" => "",
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SECTION_URL" => "",
        "SECTION_USER_FIELDS" => array("", ""),
        "SEF_MODE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_ALL_WO_SECTION" => "N",
        "SHOW_CLOSE_POPUP" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_FROM_SECTION" => "N",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "SHOW_SLIDER" => "Y",
        "SLIDER_INTERVAL" => "3000",
        "SLIDER_PROGRESS" => "N",
        "TEMPLATE_THEME" => "blue",
        "USE_ENHANCED_ECOMMERCE" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "USE_PRICE_COUNT" => "N",
        "USE_PRODUCT_QUANTITY" => "N",
        "TITLE" => "С этим товаром покупают",
        "CLASS" => 'recomend'
    )
); ?>
<?php endif?>

<?php
if (empty($arResult['PROPERTIES']['RECOMMENDED']['VALUE']) || empty($arResult['PROPERTIES']['OTHER_PRODUCTS']['VALUE'])) {
    global $similarFilter;
    $similarFilter['!ID'] = $arResult['ID'];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        [
            "ACTION_VARIABLE"                 => "action",
            "ADD_PICT_PROP"                   => "-",
            "ADD_PROPERTIES_TO_BASKET"        => "Y",
            "ADD_SECTIONS_CHAIN"              => "N",
            "ADD_TO_BASKET_ACTION"            => "ADD",
            "AJAX_MODE"                       => "N",
            "AJAX_OPTION_ADDITIONAL"          => "",
            "AJAX_OPTION_HISTORY"             => "N",
            "AJAX_OPTION_JUMP"                => "N",
            "AJAX_OPTION_STYLE"               => "N",
            "BACKGROUND_IMAGE"                => "-",
            "BASKET_URL"                      => "/personal/basket.php",
            "BROWSER_TITLE"                   => "-",
            "CACHE_FILTER"                    => "Y",
            "CACHE_GROUPS"                    => "Y",
            "CACHE_TIME"                      => "43200",
            "CACHE_TYPE"                      => "A",
            "COMPATIBLE_MODE"                 => "Y",
            "CONVERT_CURRENCY"                => "N",
            "CUSTOM_FILTER"                   => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
            "DETAIL_URL"                      => "",
            "DISABLE_INIT_JS_IN_COMPONENT"    => "N",
            "DISPLAY_BOTTOM_PAGER"            => "N",
            "DISPLAY_COMPARE"                 => "N",
            "DISPLAY_TOP_PAGER"               => "N",
            "ELEMENT_SORT_FIELD"              => "sort",
            "ELEMENT_SORT_FIELD2"             => "id",
            "ELEMENT_SORT_ORDER"              => "asc",
            "ELEMENT_SORT_ORDER2"             => "desc",
            "ENLARGE_PRODUCT"                 => "STRICT",
            "FILTER_NAME"                     => "similarFilter",
            "HIDE_NOT_AVAILABLE"              => "N",
            "HIDE_NOT_AVAILABLE_OFFERS"       => "N",
            "IBLOCK_ID"                       => "5",
            "IBLOCK_TYPE"                     => "catalog",
            "INCLUDE_SUBSECTIONS"             => "Y",
            "LABEL_PROP"                      => [],
            "LAZY_LOAD"                       => "N",
            "LINE_ELEMENT_COUNT"              => "3",
            "LOAD_ON_SCROLL"                  => "N",
            "MESSAGE_404"                     => "",
            "MESS_BTN_ADD_TO_BASKET"          => "В корзину",
            "MESS_BTN_BUY"                    => "Купить",
            "MESS_BTN_DETAIL"                 => "Подробнее",
            "MESS_BTN_SUBSCRIBE"              => "Подписаться",
            "MESS_NOT_AVAILABLE"              => "Нет в наличии",
            "META_DESCRIPTION"                => "-",
            "META_KEYWORDS"                   => "-",
            "OFFERS_LIMIT"                    => "5",
            "PAGER_BASE_LINK_ENABLE"          => "N",
            "PAGER_DESC_NUMBERING"            => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL"                  => "N",
            "PAGER_SHOW_ALWAYS"               => "N",
            "PAGER_TEMPLATE"                  => ".default",
            "PAGER_TITLE"                     => "Товары",
            "PAGE_ELEMENT_COUNT"              => "18",
            "PARTIAL_PRODUCT_PROPERTIES"      => "N",
            "PRICE_CODE"                      => ["BASE"],
            "PRICE_VAT_INCLUDE"               => "Y",
            "PRODUCT_BLOCKS_ORDER"            => "price,props,sku,quantityLimit,quantity,buttons",
            "PRODUCT_ID_VARIABLE"             => "id",
            "PRODUCT_PROPS_VARIABLE"          => "prop",
            "PRODUCT_QUANTITY_VARIABLE"       => "quantity",
            "PRODUCT_ROW_VARIANTS"            => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
            "PRODUCT_SUBSCRIPTION"            => "Y",
            // "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE"                        => "personal",
            "SECTION_CODE"                    => "",
            "SECTION_ID"                      => $arResult['SECTION']['ID'],
            "SECTION_ID_VARIABLE"             => "SECTION_ID",
            "SECTION_URL"                     => "",
            "SECTION_USER_FIELDS"             => ["", ""],
            "SEF_MODE"                        => "N",
            "SET_BROWSER_TITLE"               => "N",
            "SET_LAST_MODIFIED"               => "N",
            "SET_META_DESCRIPTION"            => "N",
            "SET_META_KEYWORDS"               => "N",
            "SET_STATUS_404"                  => "N",
            "SET_TITLE"                       => "N",
            "SHOW_404"                        => "N",
            "SHOW_ALL_WO_SECTION"             => "N",
            "SHOW_CLOSE_POPUP"                => "N",
            "SHOW_DISCOUNT_PERCENT"           => "N",
            "SHOW_FROM_SECTION"               => "N",
            "SHOW_MAX_QUANTITY"               => "N",
            "SHOW_OLD_PRICE"                  => "N",
            "SHOW_PRICE_COUNT"                => "1",
            "SHOW_SLIDER"                     => "Y",
            "SLIDER_INTERVAL"                 => "3000",
            "SLIDER_PROGRESS"                 => "N",
            "TEMPLATE_THEME"                  => "blue",
            "USE_FILTER"                      => "Y",
            "USE_ENHANCED_ECOMMERCE"          => "N",
            "USE_MAIN_ELEMENT_SECTION"        => "N",
            "USE_PRICE_COUNT"                 => "N",
            "USE_PRODUCT_QUANTITY"            => "N",
            "TITLE"                           => "Похожие товары",
            "CLASS"                           => 'other'
        ]
    );
} ?>

<!--<div class="product-slider-block">-->
<script>
    function add2wishDetail(itemId, th) {
        $.ajax({
            url: '/local/ajax/wishlist.php',
            method: 'post',
            dataType: 'html',
            data: {itemId: itemId},
            success: function (data) {
                var countWish = $('.header-bottom__favorite').data('count');
                if (data === 'add') {
                    $(th).addClass('active');
                    $(th).text('В избранном');
                    $('.header-bottom__favorite').addClass('active');
                    countWish++;
                    $('.header-bottom__favorite').data('count', countWish);
                } else {
                    $(th).removeClass('active');
                    $(th).text('В избранное');
                    countWish--;
                    $('.header-bottom__favorite').data('count', countWish);
                    if (countWish <= 0) {
                        $('.header-bottom__favorite').removeClass('active');
                    }
                }
            }
        });
    };

    function addToBasket(id, quantity, price, th) {
        $.ajax({
            url: '/local/ajax/addbasketitem.php',
            method: 'post',
            dataType: 'html',
            async: false,
            data: {productId: id, quantity: quantity, price: price},
            success: function (data) {
                $(th).text('В корзине');
                $(".go-to-basket").addClass("active");
            }
        });

        $.ajax({
            url: '/local/ajax/updatesmallbasket.php',
            method: 'post',
            dataType: 'html',
            async: false,
            data: {},
            success: function (data) {
                $("#smallCartPrice").html(data.price + '₽');
                $('.header-bottom__cart').addClass('active');
            }
        });
    }

    function changeQuantity(id, quantity, action) {
		$.ajax({
			url: '/local/ajax/changequantity.php',
			method: 'post',
			dataType: 'json',
			data: { productId: id, quantity: quantity, action: action },
			success: function(state) {
				// Обновляем интерфейс корзины с новым состоянием
				updateCartUI(state);
			},
			error: function(error) {
				console.error('Ошибка при изменении количества:', error);
			}
		});
	
		$.ajax({
			url: '/local/ajax/updatesmallbasket.php',
			method: 'post',
			dataType: 'text', // Ожидаем итоговую сумму
			success: function(totalPrice) {
				$("#smallCartPrice").html(totalPrice + ' ₽');
			},
			error: function(error) {
				console.error('Ошибка при обновлении корзины:', error);
			}
		});
	}

</script>
<?php
    $schema = [];

    if ($arResult['PRODUCT']['QUANTITY'] > 0) {
        $schema['status'] = 'https://schema.org/InStock';
    } else {
        $schema['status'] = 'https://schema.org/OutOfStock';
    }
?>
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "description": "<?= $arResult['DETAIL_TEXT'] ? htmlspecialchars($arResult['DETAIL_TEXT']) : htmlspecialchars($arResult['PREVIEW_TEXT']) ?>",
        "name": "<?= $arResult['NAME']?>",
        "image": "https://azbuka-severa.ru/<?=  $arResult['SCHEMA_PICTURE']?>",
        "brand": "Азбука Севера",
        <?php /* "model": "", */ ?>
        "offers": {
            "@type": "Offer",
            "availability": "<?= $schema['status']?>",
            "price": "<?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?: $arResult['MIN_PRICE']['VALUE']?>",
            "priceCurrency": "RUB"
        }
    }
</script>

<?php foreach ($arResult['SCHEMA_IMAGES'] as $id => $arPicture) { ?>
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "contentUrl": "https://azbuka-severa.ru<?= $arPicture['SRC'] ?>",
            "name": "<?= explode('.', $arPicture['ORIGINAL_NAME'])[0] ?>",
            "description": "<?= explode('.', $arPicture['ORIGINAL_NAME'])[0] ?>",
            "width": "<?= $arPicture['WIDTH'] ?>",
            "height": "<?= $arPicture['HEIGHT'] ?>"
        }
    </script>
<?php } ?>

