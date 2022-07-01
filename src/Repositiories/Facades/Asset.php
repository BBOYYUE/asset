<?php


namespace Bboyyue\Asset\Repositiories\Facades;


use Bboyyue\Asset\Repositiories\Interfaces\AssetFacadeInterface;
use Bboyyue\Asset\Util\RedisUtil;
use Illuminate\Support\Str;

class Asset implements AssetFacadeInterface
{

    static function createAsset($name, $work_type = 0, $asset_type = 0, $user_id = 0, $option = [], $resource_type = 0)
    {
        $uuid = Str::uuid();
        $asset = new \Bboyyue\Asset\Model\Asset();
        $asset->name = $name;
        $asset->option = json_encode($option);
        $asset->work_type = $work_type;
        $asset->asset_type = $asset_type;
        $asset->status = 0;
        $asset->uuid = $uuid;
        $asset->user_id = $user_id;
        $asset->alias = Str::before($uuid, '-');
        $asset->save();
        return $asset;
    }

    static function removeAsset($asset)
    {
        $asset->delete();
    }

    static function renameAsset($asset, $name)
    {
        $asset->name = $name;
        $asset->save();
        return $asset;
    }

    static function setOptionAsset($asset, $option)
    {
        $asset->option = json_encode($option);
        return $asset;
    }

    static function setTag($asset, $tag)
    {
        // TODO: Implement setTag() method.
    }

    static function generate($asset)
    {
        if ($asset->children->count() > 0) {
            foreach ($asset->children as $child) {
                self::generate($child);
            }
        }
        if (RedisUtil::getWaitingLen() >= config('bboyyue-asset.redis.max_waiting_len')) {
            throw new \Exception('排队数量过多');
        }
        $info = [
            'name' => $asset->name,
            'id' => $asset->id,
            'asset_type' => $asset->asset_type,
            'work_type' => $asset->work_type,
            'uuid' => $asset->uuid
        ];
        RedisUtil::addWaiting($asset->uuid);
        RedisUtil::setInfo($asset->uuid, $info);
        return $asset;
    }

    function updateResourceDocument($asset, $type = 'all')
    {
        // TODO: Implement updateResourceDocument() method.
    }
}