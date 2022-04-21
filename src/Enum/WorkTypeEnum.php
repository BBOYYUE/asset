<?php


namespace Bboyyue\Asset\Enum;


class WorkTypeEnum extends Enum
{
    /**
     * 逻辑资源
     * 逻辑资源表示它不实际绑定素材
     * 逻辑资源可能层层嵌套
     */
    const WORK = 1;
    /**
     * 实体资源
     * 实体资源绑定实际素材
     */
    const ASSET = 2;
}