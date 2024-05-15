<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    public $incrementing = false;

    protected $table = 'brand';

    protected $primaryKey = "brand_id";

    protected $fillable = [
        'brand_id',
        'brand_name',
        'status',
        'brand_group_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * Many to One relationship with BrandGroup model.
     */
    public function brandGroup(): BelongsTo
    {
        return $this->belongsTo(BrandGroup::class, 'brand_group_id');
    }

    /**
     * One to Many relationship with ProductInfoDo model.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductInfoDo::class, 'brand_id');
    }
}
