<?php


namespace Bboyyue\Asset\Model;

use Bboyyue\Asset\Repositiories\Impl\AssetModelTrait;
use Bboyyue\Asset\Repositiories\Interfaces\AssetModelInterface;
use Bboyyue\Filesystem\Model\FilesystemModel;
use Bboyyue\Filesystem\Repositiories\Interfaces\FilesystemTraitInterface;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Tags\HasTags;
use Bboyyue\Filesystem\Repositiories\Impl\FilesystemTrait;


class Asset extends Model implements Sortable, FilesystemTraitInterface, AssetModelInterface
{
    use HasFactory, HasTags, SortableTrait, CastsEnums, FilesystemTrait, AssetModelTrait;
    protected $table = 'assets';
    protected $fillable = [
        'name',
        'option',
        'work_type',
        'asset_type',
        'status',
        'order',
        'uuid',
        'alias',
        'parent_id',
        'user_id',
        'group_id',
    ];


    /**
     * 排序功能
     * @var array
     */
    public array $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];


    /**
     * 在排序时按照某个字段进行分组
     * @return mixed
     */
    public function buildSortQuery()
    {
        return static::query()->where('parent_id', $this->parent_id);
    }


    public function filesystem(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FilesystemModel::class, 'model_id','id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function folders(){
        return $this->hasMany(AssetFolder::class, 'parent_id', 'id');
    }
    public function works(){
        return $this->hasMany(AssetWork::class, 'parent_id', 'id');
    }
}