<?php


namespace Bboyyue\Asset\Work\Resource;


use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\ResourceMethodTypeEnum;
use Bboyyue\Asset\Enum\ResourceTypeEnum;
use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetWorkInterface;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Closure;

class SceneResourceDocumentWork implements AssetWorkInterface
{

    /**
     * 场景资源, 后期应该还会修改, 增加标牌, 热点, 场景角度等功能
     * @param $content
     * @param Closure $next
     */
    public function handle($content, Closure $next)
    {
        $sceneObject = Asset::where('parent_id', $content['id'])->get();
        $nextSceneList = [];
        foreach ($sceneObject as $val) {
            if ($val->asset_type != AssetTypeEnum::ASSET && $val->resource_type != ResourceTypeEnum::SCENE) continue;
            /**
             * 获取场景资源绑定的文件
             */
            $file = $val->listFilesystemData();
            $pano = null;
            $xml = null;
            foreach ($file as $v) {
                if ($v->use_type == FilesystemDataTypeEnum::PANORAMA_TILES) {
                    $pano = $v;
                } elseif ($v->use_type == FilesystemDataTypeEnum::PANORAMA_XML) {
                    $xml = $v;
                } elseif ($v->use_type == FilesystemDataTypeEnum::PANORAMA_THUMB_IMG){
                    $thumb = $v;
                }
            }
            /**
             * todo 现在只绑定了各种文件, 后期还要绑定其他资源
             */
            $data = [
                'id' => $val->id,
                'name' => $val->name,
                'uuid' => $val->uuid,
                'thumb' => $thumb ? $thumb->linePath() : '',
                'tile' => $pano ? $thumb->linePath(): '',
                'xml' => $xml ? $xml->linePath() : '',
            ];

            /**
             * 如果有 xml, 会把xml读出来然后赋值到 krpano 键
             */
            if ($xml && $xml->linePath(ResourceMethodTypeEnum::GENERATE_SCENE)) {
                $data['krpano'] = file_get_contents('http://192.168.10.10:9000/alpha-api/' . $xml->linePath());
            }
            $nextSceneList[] = $data;
        }
        $content['scene_list'] = $nextSceneList;
        $next($content);
    }
}