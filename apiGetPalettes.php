<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $p = $m->getPalettes();
    return $p;
}, $mosaic);
?>