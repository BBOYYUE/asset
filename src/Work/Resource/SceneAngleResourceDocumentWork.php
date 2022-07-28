<?php


namespace Bboyyue\Asset\Work\Resource;

use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\ResourceTypeEnum;
use Closure;
use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetWorkInterface;

class SceneAngleResourceDocumentWork implements AssetWorkInterface
{
    public function handle($content, Closure $next)
    {
        $sceneAngleObject = Asset::where([
            ['parent_id','=', $content['id']],
            ['asset_type', '=', AssetTypeEnum::ASSET],
            ['resource_type', '=', ResourceTypeEnum::SCENE_ANGLE]
        ])->get();
        $nextSceneAngleList = [];
        foreach ($sceneAngleObject as $val) {
            $option = json_decode($val->option);

            $data = [
                'id' => $val->id,
                'name' => $val->name,
                'atv' => $option->atv,
                'ath' => $option->ath,
                'fov' => $option->fov,
                'fovMin' =>$option->distanceMap[0],
                'fovMax' =>$option->distanceMap[1],
                'horizontalMin' => $option->horizontalMap[0],
                'horizontalMax' => $option->horizontalMap[1],
                'verticalMin' => $option->verticalMap[0],
                'verticalMax' => $option->verticalMap[1],
                'distanceMap' => $option->distanceMap,
                'horizontalMap' => $option->horizontalMap,
                'verticalMap' => $option->verticalMap
            ];
            $nextSceneAngleList[] = $data;
        }
        $content['scene_angle_list'] = $nextSceneAngleList;
        $next($content);
    }
}