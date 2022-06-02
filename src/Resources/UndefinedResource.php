<?php


namespace Bboyyue\Asset\Resources;


use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;

trait UndefinedResource
{
    function undefinedResource($request)
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
        ];
    }

    function undefinedWorkResource($request)
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
        ];
    }

    function undefinedDirResource($request)
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
        ];
    }
}