<?php
require_once("classMosaic.php");
if (isset($_POST['data']['id']) && isset($_POST['data']['image'])) {
    $m = new Mosaic($_POST['data']['id']);
} else {
    http_response_code(400);
    die("ID is empty");
}
header('Content-Type: application/json');
$ret = [];
try {
    $ret["data"] = $m->getFragment($_POST['data']['image'], $_POST['data']['x'], $_POST['data']['y']);
    $ret["result"] = "OK";
} catch (Throwable | Exception | MException $e) {
    $ret["result"] = "FAIL";
    $ret["description"] = $e->getMessage();
}
echo json_encode($ret, JSON_HEX_APOS | JSON_HEX_QUOT);
?>