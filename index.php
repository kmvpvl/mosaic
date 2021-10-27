<!DOCTYPE html>
<?php
require_once('classMosaic.php');
require_once('multilang.php');
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="bootstrap.css">
<link rel="stylesheet" href="mosaic.css">
<script src="scripts/jquery360.js"></script>
<script src="scripts/bootstrap.min.js"></script>
<script src="eventhandler.js"></script>
<script src="mosaic.js"></script>
<title><?=getSpecString(7)?></title>
</head>
<body>
<loading-wait class="spinner-border"></loading-wait>
<!--img src="fishes.jpg"-->
<logo>Logo</logo>
<navigation>
<div><?=getSpecString(7)?></div>
<step class="disable" step="upload"><tip><?=getSpecString(1)?></tip></step>   
<step class="disable" step="size"><tip><?=getSpecString(2)?></tip></step>    
<step class="disable" step="palette"><tip><?=getSpecString(3)?></tip></step>    
<step class="disable" step="calculate"><tip><?=getSpecString(4)?></tip></step>    
<step class="disable" step="order"><tip><?=getSpecString(5)?></tip></step>    
<step class="disable" step="track"><tip><?=getSpecString(6)?></tip></step>    
</navigation>
<images>
</images>
<curstep>
<step-upload>
    <instructions>
    <?=getSpecString(0)?>
    </instructions>
    <current-image>
        <img-frame>
        <div>        <input type="file" id="inImage"></div>
    <img id="imgPreview">
    </img-frame>
    </current-image>
    <input-data>
        <input type="text" id="customerName">
        <input type="hidden" id="ulrImage">
        <button id="btnUploadImage">&#8594;</button>
    </input-data>
</step-upload>  
<step-size>
    <instructions>
        <div>1. <?=getSpecString(9)?></div>
        <div>2. <?=getSpecString(8)?>
        <input type="number" id="pannoWidth">
        </div>
        <div>3. <?=getSpecString(10)?><?=getSpecString(11)?><?=getSpecString(12)?></div>
    </instructions>
    <current-image>
        <span></span>
        <div><input type="number" crop="left"></div>
        <span></span>
        <div><input type="number" crop="top"></div>
        <img-frame>
        <span class="crop-left"></span>
        <span class="crop-right"></span>
        <span class="crop-top"></span>
        <span class="crop-bottom"></span>
        <img id="imgRaw"></img-frame>
        <div style="display: flex;flex-direction: column;justify-content: flex-end;"><input type="number" crop="bottom"></div>
        <span></span>
        <div style="text-align:right;"><input type="number" crop="right"></div>
        <span></span>
    </current-image>
    <input-data>
        <button id="btnPannoSize">&#8594;</button>
    </input-data>
</step-size>  
<step-palette>
<instructions>
        <div><?=getSpecString(13)?></div>
        <palettes>
        </palettes>
    </instructions>
    <current-image>
        <img-frame>
        <img id="imgSized">
        </img-frame>
    </current-image>
    <input-data>
        <button id="btnAttachPalette">&#8594;</button>
    </input-data>
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
function invalidateValues() {
    //invalidate values
    let v;
    v = parseInt($('#pannoWidth').val());
    if (isNaN(v) || v < 0) 
        $('#pannoWidth').addClass('form-control is-invalid');
    else 
        $('#pannoWidth').removeClass('form-control is-invalid');

    v = parseInt($('input[crop="left"]').val());
    if (isNaN(v) || v < 0) 
        $('input[crop="left"]').addClass('form-control is-invalid');
    else 
        $('input[crop="left"]').removeClass('form-control is-invalid');

    v = parseInt($('input[crop="top"]').val());
    if (isNaN(v) || v < 0) 
        $('input[crop="top"]').addClass('form-control is-invalid');
    else 
        $('input[crop="top"]').removeClass('form-control is-invalid');

    v = parseInt($('input[crop="right"]').val());
    if (isNaN(v) || v < 0) 
        $('input[crop="right"]').addClass('form-control is-invalid');
    else 
        $('input[crop="right"]').removeClass('form-control is-invalid');

    v = parseInt($('input[crop="bottom"]').val());
    if (isNaN(v) || v < 0) 
        $('input[crop="bottom"]').addClass('form-control is-invalid');
    else 
        $('input[crop="bottom"]').removeClass('form-control is-invalid');

}
function resetValues() {
    $('#customerName').val('');
    $('#inImage').val('');
    $('input[crop="left"]').val(0);
    $('input[crop="top"]').val(0);
    $('input[crop="right"]').val(0);
    $('input[crop="bottom"]').val(0);
}
function updateValues(lsdata) {
    mosaic = lsdata;
    resetValues();
    $('#customerName').val(mosaic.name);
    //get current image and fill images list
    let curimage = null;
    let curstep = 'upload';
    $('images').html('<div thumb="New" class="active">New</div>');
    if ('image' in mosaic) {
        curstep = 'size';
        if ('length' in mosaic.image) {
            for (let[i,v] of Object.entries(mosaic.image)) {
                if (mosaic.currentimage == v.id) {
                    curimage = v;
                }
                $('images').append('<div thumb="'+v.id+'"><img src="images/raw/'+v.filename+'"></div>');
            }
        } else {
            curimage = mosaic.image;
            $('images').append('<div thumb="'+mosaic.image.id+'"><img src="images/raw/'+mosaic.image.filename+'"></div>');
        }
        $('div[thumb]').removeClass('active');
        $('div[thumb="'+mosaic.currentimage+'"]').addClass('active');
    }
    $('images > div[thumb]').click(function(){
        if (!$(this).hasClass('active')) {
            resetValues();
            if ($(this).attr('thumb') != 'New')
            sendDataToServer("apiSetCurrentImage", {currentimage: $(this).attr('thumb')},
                function(data, status){
                var ls = recieveDataFromServer(data, status);
                if (ls && ls.result=='OK') {
                    updateValues(ls.data);
                } else {
                    //debugger;
                    showError('Could not get pelettes!');
                }
            }); else {

            }
        }
    });
    // fill all data
    if (curimage) {
        $('#imgRaw').attr('src', 'images/raw/'+curimage.filename+'?v='+Math.random());
        if ('height' in curimage) {
            curstep = 'palette';
            $('#imgSized').attr('src', 'images/sized/'+curimage.filename+'?v='+Math.random());
            $('#pannoWidth').val(curimage.width);
            $('input[crop="left"]').val(curimage.cropleft);
            $('input[crop="top"]').val(curimage.croptop);
            $('input[crop="right"]').val(curimage.cropright);
            $('input[crop="bottom"]').val(curimage.cropbottom);
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
                s += '<chip color="'+palettes[curimage.palette].colormap[k]+'"><palette-chip style="background-color:'+palettes[curimage.palette].colormap[k]+'"></palette-chip>'+k+';'+v+'</chip>'
            }
            $('order').append('<chips>'+s+'</chips>');
        }
    }
    $('step[step]').removeClass('active');
    $('step[step]').addClass('disable');
    $('step[step="'+curstep+'"]').removeClass('disable');
    $('step[step="'+curstep+'"]').addClass('active');
    $('step[step="'+curstep+'"]').prevAll().removeClass('disable');

    invalidateValues();

    showStep(curstep);
}
$(document).ready(function(){
    sendDataToServer("apiGetPalettes", undefined,
        function(data, status){
        var ls = recieveDataFromServer(data, status);
        if (ls && ls.result=='OK') {
            palettes = ls.data.palettes;
            for (let [k, p] of Object.entries(palettes)) {
                po = new Palette(p);
                let d = $("<div></div>")
                d.append('<input type="radio" name="radioPalette" palette="'+k+'">'+k);
                d.append(po.element);
                $('palettes').append(d);
            }
            localStorage.setItem('userid', ls.data.mosaic.id);
            updateValues(ls.data.mosaic);
        } else {
            //debugger;
            showError('Could not load palettes!');
        }
    });
    $('input[crop]').change(function(){
        let xratio = $('#imgRaw').innerWidth()/$('#imgRaw')[0].naturalWidth;
        let yratio = $('#imgRaw').innerHeight()/$('#imgRaw')[0].naturalHeight;
        let side = $(this).attr('crop');
        let x, y, off;
        switch (side) {
            case 'left':
                x = $('input[crop="left"]').val();
                off = $('#imgRaw').position().left;
                $('span.crop-left').css({left:(x*xratio+off-$('span.crop-left').outerWidth()/2)})
                break;
        
            case 'right':
                x = $('input[crop="right"]').val();
                off = $('#imgRaw').position().left+$('#imgRaw').innerWidth();
                $('span.crop-right').css({left:(-x*xratio+off-$('span.crop-right').outerWidth()/2)})
                break;
        
            case 'top':
                y = $('input[crop="top"]').val();
                off = $('#imgRaw').position().top;
                $('span.crop-top').css({top:(y*yratio+off-$('span.crop-top').outerHeight()/2)})
                break;

            case 'bottom':
                y = $('input[crop="bottom"]').val();
                off = $('#imgRaw').position().top+$('#imgRaw').innerHeight();
                $('span.crop-bottom').css({top:(-y*yratio+off-$('span.crop-bottom').outerHeight()/2)})
                break;

            default:
                break;
        }
        //debugger;
        invalidateValues();
    });
});   
$('#btnPannoSize').click(function(){
    sendDataToServer("apiSetPannoSize", {
        image: mosaic.currentimage,
        width: $('#pannoWidth').val(),
        cropleft: $('input[crop="left"]').val(),
        croptop: $('input[crop="top"]').val(),
        cropright: $('input[crop="right"]').val(),
        cropbottom: $('input[crop="bottom"]').val()
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