<?php


namespace Bboyyue\Asset\Repositiories\Interfaces;

use Closure;
/**
 * Interface FilesystemWork
 * 资源管理系统管道契约
 * @package Bboyyue\Filesystem\Repositiories\Interfaces
 */
interface AssetWorkInterface
{
    public function handle($content, Closure $next);
}