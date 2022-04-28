<?php


namespace Bboyyue\Asset\Repositiories\Services\Panorama;


use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetServiceInterface;
use Bboyyue\Asset\Util\KrpanoUtil;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;

class RefreshPanoramaXmlService implements AssetServiceInterface
{

    static function run($message, $workerId)
    {
        $asset = Asset::find($message['id']);
        RedisUtil::setProgress($asset->id, 10);
        echo __CLASS__ . " ". $asset->id. " 开始! \t\n";
        do {
            $xml = $asset->listFilesystem(FilesystemTypeEnum::DATA, '' , ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first();
            if($xml) $xmlPath = $xml->localPath();
        }while(!$xml &&! isset($xmlPath));
        KrpanoUtil::setPanoramaNameAndTitle($xmlPath, $asset->alias);
        echo __CLASS__ . " ". $asset->id. " 完成!\t\n";
        RedisUtil::setProgress($asset->id, 100);
    }
}