<?php


namespace Bboyyue\Asset\Resources;


use Bboyyue\Asset\Resources\AssetResource;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;

trait ThreeResource
{
    function threeResource($request)
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
            'obj'=>$this->listFilesystem(FilesystemTypeEnum::DATA, null, ['use_type'=> FilesystemDataTypeEnum::THREE_OBJ])->first(),
            'mtl'=>$this->listFilesystem(FilesystemTypeEnum::DATA,  null, ['use_type'=> FilesystemDataTypeEnum::THREE_MATERIAL])->first(),
        ];
    }

    function threeWorkResource($request)
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
            'threes' => AssetResource::collection($this->children)
        ];
    }

    function threeDirResource($request)
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
            'folder' => AssetResource::collection($this->children)
        ];
    }
}