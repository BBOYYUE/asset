<?php


namespace Bboyyue\Asset\Resources;


use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\WorkTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;


class AssetResource extends JsonResource
{
    use PanoramaResource, ThreeResource, UndefinedResource;
    const RESOURCE_IMPL = [
        WorkTypeEnum::UNDEFINED => [
            AssetTypeEnum::WORK => 'undefinedWorkResource',
            AssetTypeEnum::ASSET => 'undefinedResource',
            AssetTypeEnum::DIR => 'undefinedDirResource'
        ],
        WorkTypeEnum::PANORAMA => [
            AssetTypeEnum::WORK => 'panoramaWorkResource',
            AssetTypeEnum::ASSET => 'panoramaResource',
            AssetTypeEnum::DIR => 'panoramaDirResource'
        ],
        WorkTypeEnum::DESIGN => [

        ],
        WorkTypeEnum::THREE => [
            AssetTypeEnum::WORK => 'threeWorkResource',
            AssetTypeEnum::ASSET => 'threeResource',
            AssetTypeEnum::DIR => 'threeDirResource'
        ],
        WorkTypeEnum::SEQUENCE => [

        ]
    ];

    public function toArray($request)
    {
        $asset = self::RESOURCE_IMPL[$this->work_type];
        $impl = $asset[$this->asset_type];
        return $this->{$impl}($request);
    }


}