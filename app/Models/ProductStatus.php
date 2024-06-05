<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStatus extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $primaryKey = 'product_status_id';

    protected $table = 'product_status';

    protected $fillable = [
        'product_status_id',
        'product_status_name',
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
     * One to Many relationship with ProducInfoDo model.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductInfoDo::class, 'prod_status_id');
    }
}
