<?php


namespace Bboyyue\Asset\Enum;


use BenSampo\Enum\Enum;

class ResourceTypeEnum extends Enum
{
    /**
     * 资源本体
     */
    const ASSET_SOURCE = 0;

    /**
     * 场景分组
     */
    const SCENE_GROUP = 1;

    /**
     * 场景
     */
    const SCENE = 2;

    /**
     * 地图列表
     */
    const MAP = 3;

    /**
     * 热点分组
     */
    const HOTSPOT_GROUP = 4;

    /**
     * 热点
     */
    const HOTSPOT = 5;

    /**
     * 切图列表
     */
    const IMG = 6;

    /**
     * 场景角度
     */
    const SCENE_ANGLE = 7;

    /**
     * 事件列表
     */
    const EVENT_LIST = 8;

    /**
     * 文件列表
     */
    const FILE_LIST = 9;
}