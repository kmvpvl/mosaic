<?php
require_once("prepareResponse.php");
echo prepareJsonResponseData(function($m){
    $m->username = $_POST['data']['username'];
    if (isset($_FILES['data'])) {
        $m->newUploadedImage($_FILES['data']['name']['file'], $_FILES['data']['tmp_name']['file']);
    }
    return $m;
}, $mosaic);
?>