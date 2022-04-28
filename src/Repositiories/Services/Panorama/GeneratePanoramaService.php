<?php


namespace Bboyyue\Asset\Repositiories\Services\Panorama;


use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetServiceInterface;
use Bboyyue\Asset\Util\KrpanoUtil;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;
use Illuminate\Support\Str;

class GeneratePanoramaService implements AssetServiceInterface
{
    /**
     * 全景生成器
     * 通过一个 jpg 文件生成
     * tile 文件
     * xml 文件
     * thumb 缩略图文件
     * 这个服务是跑在线程池里面的, 所以不用纠结运行时间的问题
     */
    static function run($message, $workerId)
    {

        $asset = Asset::find($message['id']);
        echo __CLASS__ . " ". $asset->id. " 开始! \t\n";
        $jpg =  $asset->listFilesystem(FilesystemTypeEnum::DATA, 'jpg', ['use_type' => FilesystemDataTypeEnum::PANORAMA_IMG])->first();
        RedisUtil::setProgress($asset->id, 10);
        KrpanoUtil::generatePanorama($jpg->localPath(), $workerId);
        RedisUtil::setProgress($asset->id, 20);
        KrpanoUtil::generatePanoramaThumb($jpg->localPath(), $workerId);
        RedisUtil::setProgress($asset->id, 30);
        /**
         * 操作完成之后保存生成的文件
         */
        $dir = Str::beforeLast($jpg->localPath(), '.');
        $name = Str::beforeLast($jpg->alias, '.');
        $xmlPath = $dir."/".$name.'.xml';
        $small = $dir."/small.jpg";
        $tile = $dir."/".$name.'.tiles';

        if(is_file($small)){
            $asset->addFilesystemData($small, ['use_type' => FilesystemDataTypeEnum::PANORAMA_THUMB_IMG]);
        }
        if(is_dir($tile)){
            $asset->addFilesystemData($tile, ['use_type' => FilesystemDataTypeEnum::PANORAMA_TILES]);
        }
        RedisUtil::setProgress($asset->id, 50);
        do {
            $tile = $asset->listFilesystem(FilesystemTypeEnum::DATA, '' , ['use_type' => FilesystemDataTypeEnum::PANORAMA_TILES])->first();
            if($tile) $tilePath = $tile->linePath();
        }while(!$tile &&! isset($tilePath));
        KrpanoUtil::setPanoramaTilesPath($xmlPath, $tilePath);
        KrpanoUtil::setPanoramaNameAndTitle($xmlPath, $asset->alias);

        if(is_file($xmlPath)) {
            $asset->addFilesystemData($xmlPath, ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML]);
        }

        echo __CLASS__ . " ". $asset->id. " 完成!\t\n";
        RedisUtil::setProgress($asset->id, 100);
    }
}