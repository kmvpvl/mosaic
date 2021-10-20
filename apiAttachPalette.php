<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $m->attachPalette($_POST['data']['image'], $_POST['data']['palette']);
    return $m;
}, $mosaic);
