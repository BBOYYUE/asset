<?php

namespace Bboyyue\Asset\Model\MongoDb;


use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Created by : PhpStorm
 * User: bboyyue
 * Email bboyyue@outlook.com
 * Date: 2022/6/11
 * Time: 19:37
 */
class PanoramaModel extends Model
{
    protected $connection  = 'mongodb';
    protected $collection = 'panorama_document';
    protected $fillable = ['krpano'];
}
