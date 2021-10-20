<?php
require_once('classMosaic.php');
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
<loading-wait class="spinner-border"></loading-wait>
<!--img src="fishes.jpg"-->
<logo>Logo</logo>
<navigation>
<step class="disable" step="upload">Upload image</step>    
<step class="disable" step="size">Crop & size</step>    
<step class="disable" step="palette">Choose palette</step>    
<step class="disable" step="calculate">$ Calculate</step>    
<step class="disable" step="order">Order</step>    
<step class="disable" step="track">Track</step>    
</navigation>
<images>

</images>
<curstep>
<step-upload>
    1. Let us call you by your name. Pls fill your name here <input type="text" id="customerName"><br>
    2. Upload image 
    <input type="file" id="inImage"> <br>or paste link to image
    <input type="text" id="ulrImage"><br>
    <button id="btnUploadImage">Next step...</button>
</step-upload>  
<step-size>
    <img id="imgRaw">
    1. Set new width of panno in chips<br>
    <input type="number" id="pannoWidth"><br>
    2. Crop the image
    <button id="btnAdjustSize">Recalc size...</button>
    <button id="btnPannoSize">Next step...</button>
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
    1. Change calc<br>
    <button id="btnCalculate">Next step...</button>
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
    }
    $('step[step]').removeClass('active');
    $('step[step]').addClass('disable');
    $('step[step="'+curstep+'"]').removeClass('disable');
    $('step[step="'+curstep+'"]').addClass('active');
    $('step[step="'+curstep+'"]').prevAll().removeClass('disable');
    showStep(curstep);
}
$(document).ready(function(){
    sendDataToServer("apiGetMosaic", undefined,
        function(data, status, xhr){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            localStorage.setItem('userid', ls.data.id);
            updateValues(ls.data);
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
        } else {
            showError('Could not get user information!');
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
    localStorage.setItem('current_step', 'size');
    $('curstep > step-'+step_name).show();
}

</script>
</html>