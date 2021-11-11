<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $p = $m->getPalettes();
    return ['mosaic'=>$m, 'palettes'=>$p];
}, $mosaic);
?>