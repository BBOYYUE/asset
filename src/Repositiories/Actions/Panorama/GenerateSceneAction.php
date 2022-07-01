<?php


namespace Bboyyue\Asset\Repositiories\Actions\Panorama;


use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Util\KrpanoUtil;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;
use Bboyyue\Filesystem\Model\FilesystemModel;
use Illuminate\Support\Str;

class GenerateSceneAction
{
    static function run($message, $workerId)
    {
        $asset =  Asset::find($message['id']);
        echo __CLASS__ . " ". $asset->id. " 开始! \t\n";
        $jpg =  $asset->listFilesystem(FilesystemTypeEnum::DATA, 'jpg', ['use_type' => FilesystemDataTypeEnum::PANORAMA_IMG])->first();
        if(!$jpg){
            $link =  $asset->listFilesystem(FilesystemTypeEnum::LINK, 'jpg', ['use_type' => FilesystemDataTypeEnum::PANORAMA_IMG])->first();
            $jpg = FilesystemModel::where('uuid', $link->uuid)->first();
        }
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
//        $asset->addFilesystemData($small, [
//            'use_type' => FilesystemDataTypeEnum::PANORAMA_THUMB_IMG,
//            'extension' => 'jpg',
//            'type' => FilesystemTypeEnum::DATA
//        ]);

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
        KrpanoUtil::savePanoramaDocument($xmlPath, $asset->uuid);
        if(is_file($xmlPath)) {
            $asset->addFilesystemData($xmlPath, ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML]);
        }

        echo __CLASS__ . " ". $asset->id. " 完成! \t\n";
        RedisUtil::setProgress($asset->id, 100);
    }
}