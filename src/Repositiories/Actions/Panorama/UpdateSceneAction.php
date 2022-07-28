<?php


namespace Bboyyue\Asset\Repositiories\Actions\Panorama;


use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\ResourceTypeEnum;
use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Util\KrpanoUtil;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;
use Bboyyue\Filesystem\Model\FilesystemModel;
use Illuminate\Support\Str;

class UpdateSceneAction
{
    static function run($message, $workerId)
    {
        $asset = Asset::find($message['id']);
        echo __CLASS__ . " " . $asset->id . " 开始! \t\n";
        try {
            $jpg = $asset->listFilesystem(FilesystemTypeEnum::DATA, 'jpg', ['use_type' => FilesystemDataTypeEnum::PANORAMA_IMG])->first();
            if (!$jpg) {
                $link = $asset->listFilesystem(FilesystemTypeEnum::LINK, 'jpg', ['use_type' => FilesystemDataTypeEnum::PANORAMA_IMG])->first();
                $jpg = FilesystemModel::where('uuid', $link->uuid)->first();
            }

            $xml =  $asset->listFilesystem(FilesystemTypeEnum::DATA, 'xml', ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first();
            RedisUtil::setProgress($asset->id, 10);
            echo $jpg->localPath() . "\r\n";
            echo "正在生成全景 \r\n";
            KrpanoUtil::generatePanorama($jpg->localPath(), $workerId);
            RedisUtil::setProgress($asset->id, 20);
            echo "正在生成缩略图 \r\n";
            KrpanoUtil::generatePanoramaThumb($jpg->localPath(), $workerId);

            RedisUtil::setProgress($asset->id, 30);
            /**
             * 操作完成之后保存生成的文件
             */
            $dir = Str::beforeLast($jpg->localPath(), '.');
            $name = Str::beforeLast($jpg->alias, '.');
            $xmlPath = $xml->localPath();
            $small = $dir . "/small.jpg";
            $tile = $dir . "/" . $name . '.tiles';

            if (is_file($small)) {
                echo "保存缩略图 \r\n";
                $asset->addFilesystemData($small, ['use_type' => FilesystemDataTypeEnum::PANORAMA_THUMB_IMG]);
            }
            if (is_dir($tile)) {
                echo "保存磁贴文件 \r\n";
                $asset->addFilesystemData($tile, ['use_type' => FilesystemDataTypeEnum::PANORAMA_TILES]);
            }
            RedisUtil::setProgress($asset->id, 50);
            $time = 0;
            do {
                /**
                 *
                 */
                echo "获取磁贴文件地址 \r\n";
                $tile = $asset->listFilesystem(FilesystemTypeEnum::DATA, '', ['use_type' => FilesystemDataTypeEnum::PANORAMA_TILES])->first();
                if ($tile) $tilePath = $tile->linePath();
                sleep(3);
                $time++;
            } while ((!$tile || !isset($tilePath)) && $time < 10);
            /**
             * 更新场景就不替换 xml 文件了.
             */

//            echo "修改 xml 文件 \r\n";
            KrpanoUtil::updatePanoramaTilesPath($xmlPath, $tilePath);
            KrpanoUtil::setPanoramaNameAndTitle($xmlPath, $asset->alias);
            echo $xmlPath;
            if (is_file($xmlPath)) {
                $asset->addFilesystemData($xmlPath, ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML]);
            }
            /**
             * 移除了旧的xml文件
             */
            $xml->delete();
            echo "更新缓存文档 \r\n";
            KrpanoUtil::savePanoramaDocument($xmlPath, $asset->uuid);
            if ($asset->asset_type == AssetTypeEnum::ASSET) {
                $parent = Asset::where('id', $asset->parent_id)->first();
                $parent->updateResourceDocument(ResourceTypeEnum::SCENE);
            } elseif ($asset->asset_type == AssetTypeEnum::WORK) {
                $asset->updateResourceDocument(ResourceTypeEnum::SCENE);
            }
        } catch (\Exception $e) {
            echo "发生错误:" + $e->getMessage();
        }
        echo __CLASS__ . " " . $asset->id . " 完成! \t\n";
//        $parent = Asset::where('id', $asset->parent_id)->first();
//        if($parent){
//            $parent->updateResourceDocument(ResourceTypeEnum::SCENE);
//        }
        RedisUtil::setProgress($asset->id, 100);
    }
}