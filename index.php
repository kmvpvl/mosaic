<?php
require_once('classMosaic.php');
$img = imagecreatefrompng("fishes.png");
$width = imagesx($img);
$height = imagesy($img);
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="mosaic.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></head>
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="eventhandler.js"></script>
<script src="mosaic.js"></script>
<body>
<palettes></palettes>
<img src="fishes.png">
<chips>
<?php
?>
<?php
?>
</chips>
</body>
<loading-wait class="spinner-border"></loading-wait>
<script>
function showLoading() {
	$("loading-wait").show();
}
function hideLoading() {
	$("loading-wait").hide();
} 
function showError(_text) {
	var e = $('<error-message class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><span></span></error-message>');
	$('body').append(e);
	hideLoading();
	e.find('span').html(_text);
	$("error-message").show();
}
function clearInstance() {
	$("instance").html("");
}
$(document).ready(function(){
    $('chip').each(function(index){
        $(this).html('<vendor_code>'+$(this).attr('vendor_code')+'</vendor_code><vchip class="'+$(this).attr('vendor_code')+'">&#9632;</vchip><chip_count>'+$(this).attr('chip_count')+'</chip_count><list_count>'+$(this).attr('list_count')+'</list_count>');
    });
    sendDataToServer("apiGetPalettes", undefined,
		function(data, status){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            palettes = ls.data;
            for (let [k, p] of Object.entries(palettes)) {
                po = new Palette(p);
                $('palettes').append(po.element);
            }
        } else {
            //debugger;
            showError('Could not get pelettes!');
        }
    });
});    
</script>
</html>