<?php
error_reporting(E_ERROR | E_STRICT);
if (!isset($_POST["userid"])) {
    http_response_code(401);
    die ("Unknown user id!");
}
require_once("classMosaic.php");
function prepareJsonResponseData($callback, $object){
    header('Content-Type: application/json');
    $ret = [];
    try {
        $ret["data"] = $callback($object);
        $ret["result"] = "OK";
    } catch (Throwable | Exception | MException $e) {
        $ret["result"] = "FAIL";
        $ret["description"] = $e->getMessage();
    }
    return json_encode($ret, JSON_HEX_APOS | JSON_HEX_QUOT);
}
function preparePngResponse ($callback, $object) {
    header('Content-Type: image/png');
    $ret = [];
    try {
        imagepng($callback($object));
    } catch (Exception $e) {
    }
}

try {
	$mosaic = new Mosaic($_POST['userid']);
} catch (Throwable | Exception | MException $e) {
	http_response_code(400);
	die ($e->getMessage());
}
?>