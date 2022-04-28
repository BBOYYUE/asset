<?php


namespace Bboyyue\Asset\Repositiories\Services\Panorama;


use Bboyyue\Asset\Enum\PanoramaTypeEnum;
use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetServiceInterface;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;

class GeneratePanoramaDirXmlService implements AssetServiceInterface
{

    static function run($message, $workerId)
    {

        /**
         * 生成全景作品的 xml
         */
        $krpano = "<krpano>";
        $asset =  Asset::find($message['id']);
        echo __CLASS__ . " ". $asset->id. " 开始!\t\n";
        RedisUtil::setProgress($asset->id, 10);
        $children = $asset->children;
        self::includeXml($children, $krpano);
        $krpano = $krpano."</krpano>";
        $asset->addFilesystemDataByText($krpano, ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML]);
        RedisUtil::setProgress($asset->id, 100);
        echo __CLASS__ . " ". $asset->id. " 完成!\t\n";
    }

    static function includeXml($children, $krpano)
    {
        foreach ($children as $child){
            if($child->asset_type === PanoramaTypeEnum::ASSET) {
                do {
                    $xml = $child->listFilesystem(FilesystemTypeEnum::DATA, '', ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first();
                    if ($xml) $xmlPath = $xml->linePath();
                } while (!$xml && !isset($xmlPath));
                $krpano = $krpano . "<include src='../" . $xmlPath . "'/>";
            }else{
                $krpano = $krpano . self::includeXml($child, $krpano);
            }
        }
        return $krpano;
    }
}