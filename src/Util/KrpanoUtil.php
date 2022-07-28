<?php


namespace Bboyyue\Asset\Util;


use Bboyyue\Asset\Model\MongoDb\PanoramaModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LSS\Array2XML;
use LSS\XML2Array;

class KrpanoUtil
{
    static public function generatePanorama($imgPath, $workerId)
    {
        $krpanoPath = Storage::disk('public')->path('krpano-cmd');
        $krpanoCmdPath = is_file($krpanoPath . '/krpanotools.exe') ? $krpanoPath . '/krpanotools.exe' : $krpanoPath . '/krpanotools';

        if($workerId > 0 ) $krpanoCmdPath = $krpanoCmdPath.$workerId;
        echo $krpanoCmdPath. ' makepano '.$krpanoPath. '/vtour-multires.config'. " ". $imgPath;
        exec("echo y|".$krpanoCmdPath. ' makepano '.$krpanoPath. '/vtour-multires.config'. " ". $imgPath );
    }
    static public function generatePanoramaThumb($imgPath, $workerId){
        $krpanoPath = Storage::disk('public')->path('krpano-cmd');
        $krpanoCmdPath = is_file($krpanoPath . '/krpanotools.exe') ? $krpanoPath . '/krpanotools.exe' : $krpanoPath . '/krpanotools';
        if($workerId > 0 ) $krpanoCmdPath = $krpanoCmdPath.$workerId;
        exec("echo y|".$krpanoCmdPath .' convert '.  $imgPath." ".  Str::beforeLast($imgPath, '.').'/small.jpg -resize=300x150');
    }

    static public function setPanoramaNameAndTitle($xmlPath, $name)
    {
        $xml = file_get_contents($xmlPath);
        $array = XML2Array::createArray($xml);
        $array['krpano']['scene']['@attributes']['name']  = "scene_". $name;
        $array['krpano']['scene']['@attributes']['title']  = $name;
        $krpano = Array2XML::createXML('krpano', $array['krpano'])->saveXML();
        $xml = Str::after($krpano, '<?xml version="1.0" encoding="UTF-8"?>');
        file_put_contents($xmlPath, $xml);
    }
    static public function updatePanoramaTilesPath($xmlPath, $tilePath)
    {
        $xml = file_get_contents($xmlPath);
        $array = XML2Array::createArray($xml);
        $array['krpano']['scene']['@attributes']['thumburl'] = '../'.$tilePath. "/thumb.jpg";
        $array['krpano']['scene']['preview']['@attributes']['url'] = '../'.$tilePath. "/preview.jpg";
        $array['krpano']['scene']['image']['cube']['@attributes']['url'] =
            '../'.$tilePath. "/". Str::afterLast($array['krpano']['scene']['image']['cube']['@attributes']['url'],'tiles/');
        echo $array['krpano']['scene']['image']['cube']['@attributes']['url'] . "\n";
        echo Str::afterLast($array['krpano']['scene']['image']['cube']['@attributes']['url'],'tiles/'). "\n";
        $krpano = Array2XML::createXML('krpano', $array['krpano'])->saveXML();
        $xml = Str::after($krpano, '<?xml version="1.0" encoding="UTF-8"?>');
        file_put_contents($xmlPath, $xml);
    }
    static public function setPanoramaTilesPath($xmlPath, $tilePath)
    {
        $xml = file_get_contents($xmlPath);
        $array = XML2Array::createArray($xml);
        $array['krpano']['scene']['@attributes']['thumburl'] = '../'.$tilePath. "/thumb.jpg";
        $array['krpano']['scene']['preview']['@attributes']['url'] = '../'.$tilePath. "/preview.jpg";
        $array['krpano']['scene']['image']['cube']['@attributes']['url'] =
            '../'.$tilePath. "/". Str::after($array['krpano']['scene']['image']['cube']['@attributes']['url'],'/');
        $krpano = Array2XML::createXML('krpano', $array['krpano'])->saveXML();
        $xml = Str::after($krpano, '<?xml version="1.0" encoding="UTF-8"?>');
        file_put_contents($xmlPath, $xml);
    }

    static public function savePanoramaDocument($xmlPath, $uuid)
    {
        $xml = file_get_contents($xmlPath);
        $array = XML2Array::createArray($xml);
        $uuid = (string) $uuid;
        $array['krpano']['uuid'] = $uuid;
        PanoramaModel::create($array);
    }
}