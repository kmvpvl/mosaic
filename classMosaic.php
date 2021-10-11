<?php
class MException extends Exception {

}

class Palette implements JsonSerializable {
    protected $name;
    protected $colormap;
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
    protected $palettes;
    function __construct()
    {
        $this->scan_palettes();
    }

    function jsonSerialize()
    {
        return null;
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
        return $this->palettes;
    }
}
?>