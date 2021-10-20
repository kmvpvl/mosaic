<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    return $m;
}, $mosaic);
?>