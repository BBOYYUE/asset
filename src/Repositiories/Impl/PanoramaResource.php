<?php


namespace Bboyyue\Asset\Repositiories\Impl;


use Bboyyue\Asset\Enum\PanoramaTypeEnum;
use Bboyyue\Asset\Resources\AssetResource;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;

trait PanoramaResource
{
    function panoramaResource($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'asset_type' => $this->asset_type,
            'work_type' =>  $this->work_type,
            'option' => $this->option,
            'order' => $this->order,
            'alias' => $this->alias,
            'parent_id' => $this->parent_id,
            'small'=>$this->listFilesystem(FilesystemTypeEnum::DATA, 'jpg', ['use_type'=> FilesystemDataTypeEnum::PANORAMA_THUMB_IMG])->first(),
            'xml'=>$this->listFilesystem(FilesystemTypeEnum::DATA, 'xml')->first(),
            "tiles"=> $this->listFilesystem(FilesystemTypeEnum::DATA, 'tiles', ['use_type'=> FilesystemDataTypeEnum::PANORAMA_TILES])->first(),
        ];
    }

    function panoramaWorkResource($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'asset_type' => $this->asset_type,
            'work_type' =>  $this->work_type,
            'option' => $this->option,
            'order' => $this->order,
            'alias' => $this->alias,
            'parent_id' => $this->parent_id,
            'xml' => $this->listFilesystem(FilesystemTypeEnum::DATA, 'xml', ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first(),
            'groups' => AssetResource::collection($this->children)
        ];
    }

    function panoramaGroupResource($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'asset_type' => $this->asset_type,
            'work_type' =>  $this->work_type,
            'option' => $this->option,
            'order' => $this->order,
            'alias' => $this->alias,
            'parent_id' => $this->parent_id,
            'xml' => $this->listFilesystem(FilesystemTypeEnum::DATA, 'xml', ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first(),
            'panoramas' => AssetResource::collection($this->children)
        ];
    }

    function panoramaDirResource($request): array
    {
        $children = $this->children;
        $folder = [];
        $works = [];
        /**
         * 这里判断了子项, 将子目录和作品区别对待
         */
        foreach ($children as $val){
            if($val->asset_type == PanoramaTypeEnum::DIR){
                array_push($folder, new AssetResource($val));
            }elseif($val->asset_type == PanoramaTypeEnum::WORK){
                array_push($works, new AssetResource($val));
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'asset_type' => $this->asset_type,
            'work_type' =>  $this->work_type,
            'option' => $this->option,
            'order' => $this->order,
            'alias' => $this->alias,
            'parent_id' => $this->parent_id,
            'xml' => $this->listFilesystem(FilesystemTypeEnum::DATA, 'xml', ['use_type' => FilesystemDataTypeEnum::PANORAMA_XML])->first(),
            'folders' => $folder,
            'works' => $works
        ];
    }
}