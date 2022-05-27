<?php


namespace Bboyyue\Asset\Enum;

use BenSampo\Enum\Enum;

class PanoramaTypeEnum extends Enum
{
    /**
     * 作品
     * 逻辑资源表示它不实际绑定素材
     * 逻辑资源可以包含多个实体资源和分组
     * 一个作品是编辑器的最小单元
     */
    const WORK = 1;


    /**
     * 实体资源
     * 实体资源绑定实际素材
     */
    const ASSET = 2;

    /**
     * 全景文件夹
     */
    const DIR = 3;

    /**
     * 分组
     */
    const GROUP = 4;

}