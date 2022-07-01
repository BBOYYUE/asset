<?php


namespace Bboyyue\Asset\Work\Resource;


use Bboyyue\Asset\Model\Asset;
use Bboyyue\Asset\Repositiories\Interfaces\AssetWorkInterface;
use Closure;

class FileResourceDocumentWork implements AssetWorkInterface
{

    /**
     * 场景资源, 后期应该还会修改, 增加标牌, 热点, 场景角度等功能
     * @param $content
     * @param Closure $next
     */
    public function handle($content, Closure $next)
    {
        $asset = Asset::where('id', $content['id'])->first();
        $file = $asset->listFilesystemData();
        $nextFileList = [];
        foreach ($file as $v) {
            $nextFileList[] = [
                'id' => $v->id,
                'uuid' => $v->uuid,
                'name' => $v->name,
                'source_name' => $v->source_name,
                'filesize' => $v->filesize,
                'use_type' => $v->use_type,
                'extension' => $v->extension,
                'created_at' => $v->created_at,
                'updated_at' => $v->updated_at,
                'order' => $v->order,
                'file_path' => $v->getMedia('filesystem')->last()->getPath()
            ];
        }
        $content['file_list'] = $nextFileList;
        $next($content);
    }
}