<?php


namespace Bboyyue\Asset\Repositiories\Impl;


use Bboyyue\Asset\Util\RedisUtil;

trait AssetModelTrait
{

    function addAsset($asset)
    {
        $asset->parent_id = $this->id;
        $asset->save();
        return $this;
    }

    function listChildAsset()
    {
        return $this->children();
    }

    function sortChildAsset($sort)
    {
        self::setNewOrder($sort);
        return $this;
    }

    function setChildAssetSort($child, $index)
    {

        // TODO: Implement setChildAssetSort() method.
    }

    function removeChildAsset($child)
    {
        $child->delete();
        return $this;
    }

    function setTag($tag)
    {
        $this->attachTag($tag);
        return $this;
    }

    function setChildTag($child, $tag)
    {
        // TODO: Implement setChildTag() method.
    }


    function generate($method = '')
    {
        return $this->generateAsset($this, $method);

    }

    function generateAsset($asset, $method, $interceptor = 0)
    {
        /**
         * 这里递归生成
         */
        if($asset->children->count() > 0){
            foreach ($asset->children as $child){
                    $this->generateAsset($child, $method, $asset->id);
            }
        }
        if(RedisUtil::getWaitingLen() >= config('bboyyue-asset.redis.max_waiting_len') ){
            throw new \Exception('排队数量过多');
        }
        $info = [
            'name' => $asset->name,
            'id' => $asset->id,
            'asset_type' => $asset->asset_type,
            'work_type' => $asset->work_type,
            'uuid' => $asset->uuid,
            'method' => $method
        ];
        if($asset->id) {
            if($interceptor) {
                RedisUtil::setInterceptor($asset->id, $interceptor);
            }
            RedisUtil::addWaiting($asset->id);
            RedisUtil::setInfo($asset->id, json_encode($info));
        }
        return $asset;
    }

    function childGenerate($child, $method = '')
    {
        return $this->generateAsset($child, $method);
    }
}