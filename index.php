<?php
include_once("palette.php");
$img = imagecreatefrompng("fishes.png");
#var_dump($img);
$width = imagesx($img);
$height = imagesy($img);
$palette_hist = [];
for ($x = 0; $x < $width; $x++){
    for ($y = 0; $y < $height; $y++){
        $c = imagecolorat($img, $x, $y);
        //var_dump(array('x'=>$x, 'y'=>$y, 'c'=>$c));
        if ($c === false) exit;
/*         $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $c = imagecolorexact($img, $r, $g, $b);
 */        if (!array_key_exists($c, $palette_hist)) $palette_hist[$c] = 0;
        $palette_hist[$c]++;
    }
}
$buy = [];
$buy['unknown'] = 0;
$all = 0;
foreach($palette_hist as $ci=>$count) {
    $rgb = imagecolorsforindex($img, $ci);
    $c = $rgb['red']*256*256+$rgb['green'] * 256+ $rgb['blue'];
    if (array_key_exists($c, $colors)) $buy[$colors[$c]] = $count;
    else $buy['unknown'] += $count;
    $all += $count;
}
arsort($buy);
//var_dump($buy);
//var_dump($all);
?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="palette.css">
<link rel="stylesheet" href="mosaic.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"></head>
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<body>
<img src="fishes.png">
<chips>
<?php
$total_lists = 0;
foreach ($buy as $art=>$count) {
?>
    <chip vendor_code="<?=$art?>" chip_count="<?=$count?>" list_count="<?=ceil($count/1000)?>"></chip>
<?php
    $total_lists += ceil($count/1000);    
}
?>
</chips>
<?=$total_lists?>
<div id="csv"></div>
</body>
<script>
$(document).ready(function(){
    $('chip').each(function(index){
        $(this).html('<vchip class="'+$(this).attr('vendor_code')+'">&#9632;</vchip><vendor_code>'+$(this).attr('vendor_code')+'</vendor_code><list_count>'+$(this).attr('list_count')+'</list_count><chip_count>'+$(this).attr('chip_count')+'</chip_count>');
        $('#csv').append($(this).attr('vendor_code')+';'+$(this).attr('list_count')+';'+$(this).attr('chip_count')+'<br>');
    });
});    
</script>
</html>