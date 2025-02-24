<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
?>
<div class="special-content">
    <div class="container">
        <div class="special-content__wrapper">
            <form class="order-processed">
                <p class="login-form__caption">Скоро мы позвоним вам чтобы уточнить детали, а пока</p>

                <div class="subscribe__block">
                    <h2 class="caption--h2">Присоединяйтесь к нам</h2>
                    <? /*<p class="caption__description">Узнавайте о новых скидках и свежих предложениях первыми</p>
                   <div class="subscribe__tag">
                        <a style="text-align: center;" href="/catalog/taezhnaya-zdravnitsa/maslo/" class="subscribe__tag-item subscribe__tag-item--red">Скидка 10 % на все масла</a>
                        <a style="text-align: center;" href="/catalog/ryba/slabosolenaya-ryba/tugunok-s-s/" class="subscribe__tag-item subscribe__tag-item--green">Свежайший Тугунок</a>
                        <a style="text-align: center;" href="/catalog/ryba/kholodnoe-kopchenie/nelma-krupnaya-sp-khk/" class="subscribe__tag-item subscribe__tag-item--orange">Только из Коптильни</a>
                    </div> */ ?>
                    <?$APPLICATION->IncludeComponent("bitrix:sender.subscribe","main",Array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "USE_PERSONALIZATION" => "Y",
                            "CONFIRMATION" => "N",
                            "SHOW_HIDDEN" => "Y",
                            "AJAX_MODE" => "Y",
                            "AJAX_OPTION_JUMP" => "Y",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "Y",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "3600",
                            "SET_TITLE" => "N"
                        )
                    );?>
                    <p class="subscribe__politic">Нажимая кнопку «Подписаться», вы соглашаетесь с <a download="" href="/politica.pdf">политикой конфиденциальности</a></p>

                    <a href="/" class="special-content__link special-content__link--block">На главную</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>
