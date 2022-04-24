<?php


namespace Bboyyue\Asset\Model;

use Bboyyue\Asset\Repositiories\Impl\AssetModelTrait;
use Bboyyue\Asset\Repositiories\Interfaces\AssetModelInterface;
use Bboyyue\Filesystem\Repositiories\Interfaces\FilesystemTraitInterface;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Tags\HasTags;
use Bboyyue\Filesystem\Repositiories\Impl\FilesystemTrait;


class Asset extends Model implements FilesystemTraitInterface, AssetModelInterface
{
    use HasFactory, HasTags, SortableTrait, CastsEnums, FilesystemTrait, AssetModelTrait;

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
}