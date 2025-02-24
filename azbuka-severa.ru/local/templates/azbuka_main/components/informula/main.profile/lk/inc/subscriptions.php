<?php if (!empty($arProfileBlock)) { ?>
    <form action="">
        <div class="form-group">
            <div class="form-text text-primary">Вы подписаны на рассылку информации о новых скидках и свежих
                предложениях
            </div>
        </div>
        <button type="submit" class="btn btn--blue">Отменить подписку</button>
    </form>
<?php } else { ?>
    <?php $APPLICATION->IncludeComponent("bitrix:sender.subscribe", "main", array(
            "COMPONENT_TEMPLATE"  => ".default",
            "USE_PERSONALIZATION" => "Y",
            "CONFIRMATION"        => "N",
            "SHOW_HIDDEN"         => "Y",
            "AJAX_MODE"           => "Y",
            "AJAX_OPTION_JUMP"    => "Y",
            "AJAX_OPTION_STYLE"   => "Y",
            "AJAX_OPTION_HISTORY" => "Y",
            "CACHE_TYPE"          => "A",
            "CACHE_TIME"          => "3600",
            "SET_TITLE"           => "N"
        )
    ); ?>
    <p class="subscribe__politic mrg0">Нажимая кнопку &laquo;Подписаться&raquo;, вы&nbsp;соглашаетесь с&nbsp;<a
                download="" href="/politica.pdf">политикой конфиденциальности</a></p>
<?php } ?>