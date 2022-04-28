<?php


namespace Bboyyue\Asset\Resources;


use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\PanoramaTypeEnum;
use Bboyyue\Asset\Repositiories\Impl\PanoramaResource;
use Illuminate\Http\Resources\Json\JsonResource;


class AssetResource extends JsonResource
{
    use PanoramaResource;
    const RESOURCE_IMPL = [
        AssetTypeEnum::PANORAMA => [
            PanoramaTypeEnum::WORK => 'panoramaWorkResource',
            PanoramaTypeEnum::ASSET => 'panoramaResource',
            PanoramaTypeEnum::DIR => 'panoramaDirResource'
        ],
        AssetTypeEnum::DESIGN => [

        ],
        AssetTypeEnum::THREE => [

        ],
        AssetTypeEnum::SEQUENCE => [

        ]
    ];

    public function toArray($request)
    {
        $asset = self::RESOURCE_IMPL[$this->work_type];
        $impl = $asset[$this->asset_type];
        return $this->{$impl}($request);
    }


}