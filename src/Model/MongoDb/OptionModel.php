<?php


namespace Bboyyue\Asset\Model\MongoDb;


use Jenssegers\Mongodb\Eloquent\Model;

class OptionModel extends Model
{
    protected $connection  = 'mongodb';
    protected $collection = 'asset_option';
    protected $fillable = ['option'];
}