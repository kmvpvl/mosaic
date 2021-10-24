<!DOCTYPE html>
<?php
require_once('classMosaic.php');
require_once('multilang.php');
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="mosaic.css">
<script src="scripts/jquery360.js"></script>
<script src="eventhandler.js"></script>
<script src="mosaic.js"></script>
<body>
<loading-wait class="spinner-border"></loading-wait>
<!--img src="fishes.jpg"-->
<logo>Logo</logo>
<navigation>
<step class="disable" step="upload"><?=getSpecString(1)?></step>    
<step class="disable" step="size"><?=getSpecString(2)?></step>    
<step class="disable" step="palette"><?=getSpecString(3)?></step>    
<step class="disable" step="calculate"><?=getSpecString(4)?></step>    
<step class="disable" step="order"><?=getSpecString(5)?></step>    
<step class="disable" step="track"><?=getSpecString(6)?></step>    
</navigation>
<images>

</images>
<curstep>
<step-upload>
    <instructions>
    1. Let us call you by your name. Pls fill your name here <input type="text" id="customerName"><br>
    2. <?=getSpecString(0)?><input type="file" id="inImage">
    </instructions>
    <current-image>
    </current-image>
    <input-data>
        <input type="hidden" id="ulrImage"><br>
        <button id="btnUploadImage">Next step...</button>
    </input-data>
</step-upload>  
<step-size>
    <instructions>
    1. Set new width of panno in chips<br>
        <input type="number" id="pannoWidth"><br>
        2. Crop the image
        <button id="btnAdjustSize">Recalc size...</button>
    </instructions>
    <current-image>
        <img id="imgRaw">
    </current-image>
    <input-data>
        <button id="btnPannoSize">Next step...</button>
    </input-data>
</step-size>  
<step-palette>
    <img id="imgSized">
    1. Choose palette<br>
    <palettes>
    </palettes>
    <button id="btnAttachPalette">Next step...</button>
</step-palette>  
<step-calculate>
    <img id="imgPaletted">
    1. Approve the orders parameters<br>
    <order></order>
    <button id="btnApprove">Next step...</button>
<chips></chips>
</step-calculated>  
</curstep>
</body>
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
function updateValues(lsdata) {
    mosaic = lsdata;
    $('#customerName').val(mosaic.name);
    //get current image and fill images list
    let curimage = null;
    let curstep = 'upload';
    $('images').html('');
    if ('image' in mosaic) {
        curstep = 'size';
        if ('length' in mosaic.image) {
            for (let[i,v] of Object.entries(mosaic.image)) {
                if (mosaic.currentimage == v.id) {
                    curimage = v;
                }
                $('images').append('<img thumb="'+v.id+'" src="images/raw/'+v.filename+'">');
            }
        } else {
            curimage = mosaic.image;
            $('images').append('<img thumb="'+mosaic.image.id+'" src="images/raw/'+mosaic.image.filename+'">');
        }
        $('img[thumb="'+mosaic.currentimage+'"]').addClass('active');
    }
    $('images > img[thumb]').click(function(){
        if (!$(this).hasClass('active')) {
            sendDataToServer("apiSetCurrentImage", {currentimage: $(this).attr('thumb')},
                function(data, status){
                var ls = recieveDataFromServer(data, status);
                if (ls && ls.result=='OK') {
                    updateValues(ls.data);
                } else {
                    //debugger;
                    showError('Could not get pelettes!');
                }
            });
        }
    });
    // fill all data
    if (curimage) {
        $('#imgRaw').attr('src', 'images/raw/'+curimage.filename+'?v='+Math.random());
        if ('height' in curimage) {
            curstep = 'palette';
            $('#imgSized').attr('src', 'images/sized/'+curimage.filename+'?v='+Math.random());
            $('#pannoWidth').val(curimage.width);
        }
        if ('palette' in curimage) {
            curstep = 'calculate';
            $('input[name="radioPalette"][palette="'+curimage.palette+'"]').prop('checked', true);
            $('#imgPaletted').attr('src', 'images/paletted/'+curimage.pannofilename+'?v='+Math.random());
        }
        if ('req' in curimage) {
            $('order').html('');
            $('order').append('<panno-width>'+curimage.width+'</panno-width>');
            $('order').append('<panno-height>'+curimage.height+'</panno-height>');
            let s = '';
            for (let [k,v] of Object.entries(curimage.req)){
                s += '<chip color="'+palettes[curimage.palette].colormap[k]+'">'+k+';'+v+'</chip>'
            }
            $('order').append('<chips>'+s+'</chips>');
        }
    }
    $('step[step]').removeClass('active');
    $('step[step]').addClass('disable');
    $('step[step="'+curstep+'"]').removeClass('disable');
    $('step[step="'+curstep+'"]').addClass('active');
    $('step[step="'+curstep+'"]').prevAll().removeClass('disable');
    showStep(curstep);
}
$(document).ready(function(){
    sendDataToServer("apiGetPalettes", undefined,
        function(data, status){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            palettes = ls.data;
            for (let [k, p] of Object.entries(palettes)) {
                po = new Palette(p);
                $('palettes').append(po.element);
            }
            sendDataToServer("apiGetMosaic", undefined,
                function(data, status, xhr){
                var ls = recieveDataFromServer(data, status);
                if (ls && ls.result=='OK') {
                    localStorage.setItem('userid', ls.data.id);
                    updateValues(ls.data);
                } else {
                    showError('Could not get user information!');
                }
            });
        } else {
            //debugger;
            showError('Could not load palettes!');
        }
    });
});   
$('#btnPannoSize').click(function(){
    sendDataToServer("apiSetPannoSize", {
        image: mosaic.currentimage,
        width: $('#pannoWidth').val()
    }, function(data, status, xhr){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            updateValues(ls.data);
        } else {
            showError('Could not upload image!');
        }
    });
});

$('#btnUploadImage').click(function(){
    if ($('#inImage')[0].files.length) {
        sendFileToServer("apiUploadImage", {
            file: $('#inImage')[0].files[0], username: $('#customerName').val()
        }, function(data, status, xhr){
            var ls = recieveDataFromServer(data, status);
            if (ls && ls.result=='OK') {
                updateValues(ls.data);
            } else {
                showError('Could not upload image!');
            }
        });
    } else {
        sendDataToServer("apiUploadImage", {
            username: $('#customerName').val(),
            ulrImage: $('#ulrImage').val()
        }, function(data, status, xhr){
            var ls = recieveDataFromServer(data, status);
            if (ls && ls.result=='OK') {
                updateValues(ls.data);
            } else {
                showError('Could not upload image!');
            }
        });
    }
});

$('#btnAttachPalette').click(function(){
    sendDataToServer("apiAttachPalette", {
        image: mosaic.currentimage,
        palette: $('input:radio[name="radioPalette"]:checked').attr('palette')
    }, function(data, status, xhr){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            updateValues(ls.data);
        } else {
            showError('Could not upload image!');
        }
    });
});


$('step[step]').click(function() {
    showStep($(this).attr('step'));
});

function showStep(step_name) {
    $('curstep').children().hide();
    $('curstep > step-'+step_name).show();
}

</script>
</html>