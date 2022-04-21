<?php


namespace Bboyyue\Asset\Model;

use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Tags\HasTags;

class Asset extends Model
{
    use HasFactory, HasTags, SortableTrait, CastsEnums;

    protected array $fillable = [
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