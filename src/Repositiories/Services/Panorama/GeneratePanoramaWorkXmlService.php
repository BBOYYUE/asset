<?php


namespace Bboyyue\Asset\Repositiories\Services\Panorama;


use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetServiceInterface;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;

class GeneratePanoramaWorkXmlService implements AssetServiceInterface
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
        foreach ($children as $child){
            do {
                $xml = $child->listFilesystem(FilesystemTypeEnum::DATA, '' , ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first();
                if($xml) $xmlPath = $xml->linePath();
            }while(!$xml &&! isset($xmlPath));
            $krpano = $krpano."<include src='../".$xmlPath."'/>";
        }
        $krpano = $krpano."</krpano>";
        $asset->addFilesystemDataByText($krpano, ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML]);
        RedisUtil::setProgress($asset->id, 100);
        echo __CLASS__ . " ". $asset->id. " 完成!\t\n";
    }
}