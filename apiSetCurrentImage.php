<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $m->currentimage = $_POST['data']['currentimage'];
    return $m;
}, $mosaic);
?>