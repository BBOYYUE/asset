<?php


namespace Bboyyue\Asset\Model\MongoDb;


use Jenssegers\Mongodb\Eloquent\Model;

class ResourceModel extends Model
{
    protected $connection  = 'mongodb';
    protected $collection = 'asset_resource';
    protected $fillable = ['resource'];
}