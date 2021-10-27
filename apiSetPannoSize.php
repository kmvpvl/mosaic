<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $m->setPannoWidth($_POST['data']['image'], $_POST['data']['width']
        , $_POST['data']['cropleft']
        , $_POST['data']['cropright']
        , $_POST['data']['croptop']
        , $_POST['data']['cropbottom']
    );
    return $m;
}, $mosaic);
?>