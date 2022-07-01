<?php


namespace Bboyyue\Asset\Repositiories\Interfaces;


interface AssetFacadeInterface
{
    /**
     * 创建资源
     * @param $name
     * @param array $option
     * @return mixed
     */
    static function createAsset($name, $work_type = 0, $asset_type = 0, $option = []);


    /**
     * 删除资源
     * @param $asset
     * @return mixed
     */
    static function removeAsset($asset);

    /**
     * 给某个资源修改名称
     * @param $asset
     * @param $name
     * @return mixed
     */
    static function renameAsset($asset, $name);

    /**
     * 设置某个资源的配置项
     * @param $asset
     * @param $option
     * @return mixed
     */
    static function setOptionAsset($asset, $option);

    /**
     * 给某个资源打标签
     * @param $asset
     * @param $tag
     * @return mixed
     */
    static function setTag($asset, $tag);

    /**
     * 资源可以自己通过绑定的文件夹进行生成
     * @param $asset
     * @return mixed
     */
    static function generate($asset);

    /**
     * 更新作品的资源列表缓存文档
     * @return mixed
     */
    function updateResourceDocument($asset, $type = 'all');
}