<!DOCTYPE html>
<?php
require_once('classMosaic.php');
require_once('multilang.php');
if (isset($_GET['id']) && isset($_GET['image'])) {
    $m = new Mosaic($_GET['id']);
    $im = $m->getImageXML($_GET['image']);
}
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<script src="scripts/jquery360.js"></script>
<script src="scripts/bootstrap.min.js"></script>
<link rel="stylesheet" href="bootstrap.css">
<link rel="stylesheet" href="mes.css">
<script src="eventhandler.js"></script>
<script src="mosaic.js"></script>
<title></title>
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>
</head>
<body>
<loading-wait class="spinner-border"></loading-wait>
<navigation>
<width><?=$im->width?></width>
<height><?=$im->height?></height>
<current-row>Row<input id="row" type="number"></current-row>
<current-column>Col<input id="col" type="number"></current-column>
<image-frame style="background-image: url('images/paletted/<?=$im->pannofilename?>');"></image-frame>
</navigation>
<layout>
<brief></brief>
<h-label y="home"></h-label>
<?php
for ($i = 1; $i <= 20; $i++){
?>
    <v-label x="<?=$i?>"><?=$i?></v-label>
<?php
}
?>
<row-close></row-close>
<?php
for ($i = 1; $i <= 20; $i++) {
?>
<h-label y="<?=$i?>"><?=$i?></h-label>
<?php
    for ($j = 1; $j <= 20; $j++) {
?>
<chip x="<?=$j?>" y="<?=$i?>"></chip>
<?php    
    }
?>
<row-close></row-close>
<?php
}
?>
<label-check></label-check>
</layout>
</body>
<script>
function showLoading() {
	$("loading-wait").show();
}
function hideLoading() {
	$("loading-wait").hide();
} 
function showError(_text) {
	var e = $('<error-message class="alert alert-danger alert-dismissible"><svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button><span></span></error-message>');
	$('body').append(e);
	hideLoading();
	e.find('span').html(_text);
	$("error-message").show();
}
function invalidateValues() {
}
function resetValues() {
}
function updateValues(lsdata) {
    mosaic = lsdata;
    invalidateValues();
}
function getFragment() {
    sendDataToServer("apiGetFragment", 
        {
            id: '<?=$_GET['id']?>',
            image: '<?=$_GET['image']?>',
            x:$('#col').val(), 
            y:$('#row').val()
        },
        function(data, status){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            for (i = 1; i <= 20; i++) {
                for (j = 1; j <= 20; j++) {
                    if (ls.data.panno[i-1][j-1]) {
                        $('chip[x="'+i+'"][y="'+j+'"]').text(ls.data.panno[i-1][j-1].a);
                        $('chip[x="'+i+'"][y="'+j+'"]').css('background-color', ls.data.panno[i-1][j-1].c);
                    } else {
                        $('chip[x="'+i+'"][y="'+j+'"]').text("");
                        $('chip[x="'+i+'"][y="'+j+'"]').css('background-color', '');
                    }
                }
            }
            $('brief').text("");
            for (let[k, v] of Object.entries(ls.data.brief).sort(function(a, b){
                    return a[1] < b[1] ? 1 : -1;
                })) {
                $('brief').append(''+k+'='+v+'; ');
            }
        } else {
            //debugger;
            //showError('Could not load palettes!');
        }
    });
}
$(document).ready(function(){
<?php
if (!isset($_GET['id']) || !isset($_GET['image'])) {
?>
    showError("<?=getSpecString(14)?>");
    return;
<?php    
}
?>
    $('#row').change(function(){
        getFragment();
    });
    $('#col').change(function(){
        getFragment();
    });
});   
$(window).resize(function(){
});
</script>
</html>