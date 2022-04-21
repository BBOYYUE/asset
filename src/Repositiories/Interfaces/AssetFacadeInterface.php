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
    function createAsset($name, $option=[]);

    /**
     * 删除资源
     * @param $asset
     * @return mixed
     */
    function removeAsset($asset);

    /**
     * 给某个资源修改名称
     * @param $asset
     * @param $name
     * @return mixed
     */
    function renameAsset($asset, $name);

    /**
     * 设置某个资源的配置项
     * @param $asset
     * @param $option
     * @return mixed
     */
    function setOptionAsset($asset, $option);

    /**
     * 给某个资源打标签
     * @param $asset
     * @param $tag
     * @return mixed
     */
    function setTag($asset, $tag);

    /**
     * 资源可以自己通过绑定的文件夹进行生成
     * @param $asset
     * @return mixed
     */
    function generate($asset);
}