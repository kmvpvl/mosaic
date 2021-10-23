<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $m->setPannoWidth($_POST['data']['image'], $_POST['data']['width']);
    return $m;
}, $mosaic);
?>