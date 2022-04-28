<?php


namespace Bboyyue\Asset\Repositiories\Interfaces;


interface AssetServiceInterface
{
    static function run($message, $workerId);
}