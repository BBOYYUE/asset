<?php


namespace Bboyyue\Asset\Enum;


use BenSampo\Enum\Enum;

/**
 * 其实 ThreeTypeEnum 和 PanoramaTypeEnum 没有什么区别
 * Class ThreeTypeEnum
 * @package Bboyyue\Asset\Enum
 */
class ThreeTypeEnum extends Enum
{
    /**
     * 作品
     * 逻辑资源表示它不实际绑定素材
     * 逻辑资源可能层层嵌套
     */
    const WORK = 1;
    /**
     * 实体资源
     * 实体资源绑定实际素材
     */
    const ASSET = 2;

    /**
     * 文件夹
     */
    const DIR = 3;
}