<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;

class GoodsSpareGroup extends Model
{
    protected $table = "goods_spare_groups";

    /**
     * Get the groups that owns the GoodsSpareGroup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groups(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'spare_group_id', 'id');
    }
    
}
