<?php
require_once("prepareResponse.php");
echo preparePngResponse(function($m){
    $p = $m->createBlankImage(200, 150, $_POST['data']['palette']);
    return $p;
}, $mosaic);
?>