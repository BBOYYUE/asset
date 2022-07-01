<?php


namespace Bboyyue\Asset\Model\MongoDb;


use Jenssegers\Mongodb\Eloquent\Model;

/**
 * 使用mongoDb缓存xml文件
 * Class PanoramaModel
 * @package Bboyyue\Asset\Model\MongoDb
 */

class PanoramaModel extends Model
{
    protected $connection  = 'mongodb';
    protected $collection = 'asset_krpano';
    protected $fillable = ['krpano'];
}