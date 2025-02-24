<?php
/*
foreach ($arResult['IFRAMES'] as $key => $iframe) {
    ob_start();
    printf($iframe);
    $tmp = ob_get_clean();
    $content = str_replace("#iframe_map_$key#", $tmp, $content);
}*/
$content = $arResult["CACHED_TPL"];
echo $content;