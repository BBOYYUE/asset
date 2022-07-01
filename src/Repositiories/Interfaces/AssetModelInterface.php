<?php


namespace Bboyyue\Asset\Repositiories\Interfaces;


interface AssetModelInterface
{
    /**
     * 作品可以添加子资源
     * @param $asset
     * @return mixed
     */
    function addAsset($asset);

    /**
     * @param $asset
     * @param $file_uuid
     * @return mixed
     */
    function addResource($asset, $name, $file_uuid);


    /**
     * 获取当前作品绑定的资源列表
     * @return mixed
     */
    function listChildAsset();

    /**
     * 给子资源进行排序
     * @param $sort
     * @return mixed
     */
    function sortChildAsset($sort);

    /**
     * 直接设置某子个资源的顺序号
     * @param $child
     * @param $index
     * @return mixed
     */
    function setChildAssetSort($child, $index);

    /**
     * 删除子资源
     * @param $child
     * @return mixed
     */
    function removeChildAsset($child);

    /**
     * 给自己打标签
     * @param $tag
     * @return mixed
     */
    function setTag($tag);

    /**
     * 给子资源打标签
     * @param $child
     * @param $tag
     * @return mixed
     */
    function setChildTag($child, $tag);

    /**
     * 自动获取模型下的资源并进行生成操作
     * @return mixed
     */
    function generate($method = 'generate');

    /**
     * 子资源进行生成操作
     * @param $child
     * @return mixed
     */
    function childGenerate($child, $method = 'generate');


    /**
     * 更新作品的资源列表缓存文档
     * @return mixed
     */
    function updateResourceDocument($type = 'all');


}