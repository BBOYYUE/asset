<?php


namespace Bboyyue\Asset\Repositiories\Services\Panorama;


use Bboyyue\Asset\Repositiories\Interfaces\AssetServiceInterface;

class RefreshPanoramaWorkService implements AssetServiceInterface
{

    static function run($message, $workerId)
    {
        echo __CLASS__;
    }
}