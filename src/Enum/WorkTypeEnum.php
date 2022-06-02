<?php


namespace Bboyyue\Asset\Enum;


use BenSampo\Enum\Enum;

class WorkTypeEnum extends Enum
{
    const  UNDEFINED = 0;
    /**
     * 全景
     */
    const PANORAMA = 1;
    /**
     * 平面
     */
    const DESIGN = 2;
    /**
     * 3D
     */
    const THREE = 3;
    /**
     * 序列帧
     */
    const SEQUENCE = 4;
}