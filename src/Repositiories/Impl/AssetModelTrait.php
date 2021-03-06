<?php


namespace Bboyyue\Asset\Repositiories\Impl;


use Bboyyue\Asset\Enum\AssetTypeEnum;
use Bboyyue\Asset\Enum\ResourceMethodTypeEnum;
use Bboyyue\Asset\Enum\ResourceTypeEnum;
use Bboyyue\Asset\Enum\WorkTypeEnum;
use Bboyyue\Asset\Model\MongoDb\ResourceModel;
use Bboyyue\Asset\Repositiories\Facades\Asset;
use Bboyyue\Asset\Util\RedisUtil;
use Bboyyue\Asset\Work\Resource\FileResourceDocumentWork;
use Bboyyue\Asset\Work\Resource\SceneAngleResourceDocumentWork;
use Bboyyue\Asset\Work\Resource\SceneResourceDocumentWork;
use Bboyyue\Filesystem\Enum\FilesystemDataTypeEnum;
use Bboyyue\Filesystem\Enum\FilesystemTypeEnum;
use Bboyyue\Filesystem\Model\FilesystemModel;
use Illuminate\Pipeline\Pipeline;

trait AssetModelTrait
{

    function addAsset($asset)
    {
        $asset->parent_id = $this->id;
        $asset->save();
        return $this;
    }

    function addResource($type, $name, $data)
    {
        switch ($type) {
            case ResourceTypeEnum::SCENE_ANGLE:
                $this->addResourceSceneAngel($name, $data);
                break;
            case ResourceTypeEnum::SCENE:
            default:
                $this->addResourceScene($name, $data);

        }
    }
    function updateResource($type, $data)
    {
        switch ($type){
            case ResourceTypeEnum::SCENE:
            default:
                $this->updateResourceScene($data);
        }
        $this->updateResourceDocument(ResourceTypeEnum::SCENE);
    }

    function updateResourceScene($data){
        $asset = \Bboyyue\Asset\Model\Asset::where('uuid', $data['asset_uuid'])->first();
        $oldFile = $asset->listFilesystemData();
        $canUpdateFile = true;
        foreach ($oldFile as $val){
            if($val->uuid == $data['file_uuid']) $canUpdateFile = false;
        }
        /**
         * 如果更换了全景图, 那么就要重新生成全景
         */
        if($canUpdateFile && $data['file_uuid']) {
            $file = FilesystemModel::where([
                ['uuid', '=', $data['file_uuid']],
                ['type', '=', FilesystemTypeEnum::DATA]
            ])->first();
            /**
             * 这里有个逻辑 就是只有作品资源才会绑定实体文件,
             * 作品下的资源都是绑定的作品下文件的软连接
             * 所以这里调用 removeFilesystemLink() 删除软链接绑定, 不删除源文件
             */
//            $asset->removeFilesystemLink();
            $asset->removeFilesWhere([
                ['use_type', 'in' , [1, 2, 3]]
            ]);
            $asset->addFilesystemLink(
                $data['name'],
                null,
                $file
            );
            $asset->generate(ResourceMethodTypeEnum::UPDATE_SCENE);

        }else{
            $this->updateResourceDocument(ResourceTypeEnum::SCENE);
        }
    }
    function addResourceSceneAngel($name, $data)
    {
        /**
         * 创建角度资源
         */
        $asset = Asset::createAsset(
            $name,
            WorkTypeEnum::PANORAMA,
            AssetTypeEnum::ASSET,
            $this->user_id,
            $data,
            ResourceTypeEnum::SCENE_ANGLE
        )->bindFilesystemDir();
        $this->addAsset($asset);
        $this->updateResourceDocument(ResourceTypeEnum::SCENE_ANGLE);
    }

    function addResourceScene($name, $file_uuid)
    {
        /**
         * 创建全景资源
         */
        $asset = Asset::createAsset(
            $name,
            WorkTypeEnum::PANORAMA,
            AssetTypeEnum::ASSET,
            $this->user_id,
            [],
            ResourceTypeEnum::SCENE
        )->bindFilesystemDir();
        /**
         * 给全景资源绑定全景图的快捷方式
         */
        $file =  FilesystemModel::where([
            ['uuid', '=', $file_uuid],
            ['type', '=', FilesystemTypeEnum::DATA],
            ['use_type', '=' , FilesystemDataTypeEnum::PANORAMA_IMG]
        ])->first();
        $asset->addFilesystemLink(
            $name,
            null,
            $file
        );
        $this->addAsset($asset);
        /**
         * 全景资源的生成操作
         */
        $asset->generate(ResourceMethodTypeEnum::GENERATE_SCENE);
//        $this->updateResourceDocument(ResourceTypeEnum::SCENE);
    }

    function remove($asset)
    {
        $children = $asset->children;
        foreach ($children as $child) {
            $child->clearFilesystem();
            $child->remove($child);
        }
        $asset->clearFilesystem();
        $asset->delete();
    }

    function listChildAsset()
    {
        return $this->children;
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
        return $this->generateAsset($this, $method, null);
    }


    function updateResourceDocument($type = 'all')
    {


        $document = ResourceModel::where('resource.uuid', $this->uuid)->first();
        $content = json_decode($document, true);
        $needInit = false;
        if (!$content) {
            $asset = \Bboyyue\Asset\Model\Asset::where('uuid', $this->uuid)->first();
            $content = [
                'resource' => [
                    'uuid' => $asset->uuid,
                    'id' => $asset->id
                ]
            ];
            $needInit = true;
        }

        /**
         * 这里是一个重新生成资源配置信息的管道
         */
        app(Pipeline::class)
            ->send($content['resource'])
            ->through([
                /**
                 * 全景资源的document的生成操作
                 */
                SceneResourceDocumentWork::class,
                FileResourceDocumentWork::class,
                SceneAngleResourceDocumentWork::class
            ])
            ->then(function ($content) use ($needInit) {
                if ($needInit) {
                    ResourceModel::create(['resource' => $content]);
                } else {
                    ResourceModel::where('resource.uuid', $this->uuid)->update(['resource' => $content]);
                }
            });

        // todo 重新生成资源列表文档
    }



    /**
     * @param $asset
     * @param $method
     * @param int $interceptor
     * @return mixed
     * @throws \Exception
     */
    function generateAsset($asset, $method, $interceptor = 0)
    {
        /**
         * 这里递归生成
         */
        if ($asset->children->count() > 0) {
            foreach ($asset->children as $child) {
                $this->generateAsset($child, $method, $asset->id);
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
            'uuid' => $asset->uuid,
            'method' => $method,
        ];
        if ($asset->id) {
            if ($interceptor) {
                RedisUtil::setInterceptor($asset->id, $interceptor);
            }
            RedisUtil::addWaiting($asset->id);
            RedisUtil::setInfo($asset->id, json_encode($info));
        }
        return $asset;
    }

    ################################ todo 这里也需要重构

    function childGenerate($child, $method = '')
    {
//        return $this->generateAsset($child, $method);
    }
}