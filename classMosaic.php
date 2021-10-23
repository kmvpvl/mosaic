<?php
class MException extends Exception {

}

class Palette implements JsonSerializable {
    protected $name;
    public $colormap;
    function __construct(String $palette_name, Array $colormap_array)
    {
        $this->name = $palette_name;
        $this->colormap = $colormap_array;
    }
    function jsonSerialize(){
        return [
            'name'=>$this->name,
            'colormap'=>$this->colormap
        ];
    }
}
class Mosaic implements JsonSerializable {
    protected $palettes = [];
    protected $xml = null;
    protected $userid = null;
    function __construct($_userid)
    {
        $this->userid = $_userid;
        $this->getXML($_userid);
        if (!file_exists('users')) mkdir('users', 0755, true);
        if (!file_exists('images')) mkdir('images', 0755, true);
        if (!file_exists('images/raw')) mkdir('images/raw', 0755, true);
        if (!file_exists('images/sized')) mkdir('images/sized', 0755, true);
        if (!file_exists('images/paletted')) mkdir('images/paletted', 0755, true);
    }
    function newUploadedImage(string $filename, string $tmpfile):void {
        $uname = bin2hex(openssl_random_pseudo_bytes(16));
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!move_uploaded_file($tmpfile, 'images/raw/'.$uname.'.'.$ext)) {
            throw new MException('Couldn\'t upload file');
        };
        $c = $this->xml->addChild('image');
        if (is_null($c)) throw new MException('Couldn\'t add image to XML');

        $this->xml->currentimage = $uname;
        $c->id = $uname;
        $c->name = $filename;
        $c->filename = $uname.'.'.$ext;
        $this->saveXML();
    }
    protected function getImageXML(string $imageid): SimpleXMLElement {
        if(count($this->xml->image)>1) {
            foreach($this->xml->image as $im) {
                if ($im->id == $imageid) {
                    $imagexml = $im;
                    break;
                }
            }
        } else {
            $imagexml = $this->xml->image;
        }
        return $imagexml;
    }

    function setPannoWidth(string $imageid, int $width, int $cropleft = 0, int $croptright = 0, int $croptop = 0, int $cropbottom = 0): void {
        $imagexml = $this->getImageXML($imageid);
        $jpgimage = imagecreatefromjpeg('images/raw/'.$imagexml->filename);
        $jpgimage = imagescale($jpgimage, $width, -1, IMG_BICUBIC);
        imagejpeg($jpgimage, 'images/sized/'.$imagexml->filename);
        $imagexml->width = $width;
        $imagexml->height = imagesy($jpgimage);
        $this->saveXML();
    }

    function attachPalette(string $imageid, string $palette):void {
        $req = [];
        $this->scan_palettes();
        $imagexml = $this->getImageXML($imageid);
        $jpgimage = imagecreatefromjpeg('images/sized/'.$imagexml->filename);

        $pngimage = imagecreate(intval($imagexml->width), intval($imagexml->height));
        if (!isset($this->palettes[$palette])) throw new MException('Palette "'.$palette.'" not found');
        foreach($this->palettes[$palette]->colormap as $k=>$v) {
            $rgb = sscanf($v, "#%02x%02x%02x");
            imagecolorallocate($pngimage, $rgb[2], $rgb[1], $rgb[0]);
        }
        for($x = 0; $x < $imagexml->width; $x++) {
            for($y = 0; $y < $imagexml->height; $y++) {
                $rgb = imagecolorat($jpgimage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $c = imagecolorclosest($pngimage, $r, $g, $b);
                imagesetpixel($pngimage, $x, $y, $c);
                $crgb = imagecolorsforindex($pngimage, $c);
                $pkey = array_search(sprintf('#%02x%02x%02x', $crgb['blue'], $crgb['green'], $crgb['red']), $this->palettes[$palette]->colormap);
                if (!array_key_exists($pkey, $req)) $req[$pkey] = 0;
                $req[$pkey]++;
            }
        }
        arsort($req);
        $imagexml->req = "";
        foreach($req as $kreq=>$vreq) {
            $imagexml->req->$kreq = $vreq;
        }
        $bname = pathinfo($imagexml->filename, PATHINFO_FILENAME);
        $imagexml->palette = $palette;
        $imagexml->pannofilename = $bname.'.png';
        imagepng($pngimage, 'images/paletted/'.$imagexml->pannofilename);
        $this->saveXML();
    }

    function jsonSerialize()
    {
        return $this->xml;
    }

    function __set($name, $value)
    {
        switch($name) {
            case 'username': 
                $this->xml->name = $value;
            break;
            case 'currentimage':
                $this->xml->currentimage = $value;
                $this->saveXML();
                break;
            default:
                throw new MException('Unknown property');
        }
        $this->saveXML();
    }
    protected function scan_palettes():void {
        $files = scandir('palettes');
        foreach($files as $f) {
            $tname = explode('.php', $f);
            if (count($tname) > 0) {
                $pname = explode('_', $tname[0]);
                if (count($pname) == 2 && 'palette' == $pname[0]) {
                    $parr = include('palettes/'.$f);
                    $this->palettes[$pname[1]] = new Palette($pname[1], $parr);
                }
            }
        }
    }
    function getPalettes(): array {
        if (!count($this->palettes)) $this->scan_palettes();
        return $this->palettes;
    }

    protected function getXML(?string $_userid = null):SimpleXMLElement {
        if ($_userid) {
            $this->xml = simplexml_load_file('users/u-'.$_userid.'.xml');
        } 
        if ($this->xml === false || is_null($this->xml)) {
            $this->userid = $_userid?$_userid:bin2hex(openssl_random_pseudo_bytes(16));
            $this->xml = new SimpleXMLElement('<mosaic/>');
            $this->xml->addChild('id', $this->userid);
            $dt = new DateTime();
            $this->xml->created = $dt->format(DateTime::RFC1036);
            $this->saveXML();
        }
        return $this->xml;
    }
    protected function saveXML() {
        $dt = new DateTime();
        $this->xml->changed = $dt->format(DateTime::RFC1036);
        if (!$this->xml->asXML('users/u-'.$this->userid.'.xml')) throw new MException('Couldn\'t save the information');
    }
}
?>