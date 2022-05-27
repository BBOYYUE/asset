<?php


namespace Bboyyue\Asset\Scope;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AssetWorkScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('asset_type', 1);
    }
}