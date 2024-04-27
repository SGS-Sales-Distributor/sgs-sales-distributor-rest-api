<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductInfoDo extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    public $incrementing = false;
    
    protected $primaryKey = 'prod_number';

    protected $table = 'product_info_do';

    protected $fillable = [
        'prod_number',
        'prod_barcode_number',
        'prod_universal_number',
        'prod_name',
        'prod_base_price',
        'prod_unit_price',
        'prod_promo_price',
        'prod_special_offer',
        'prod_special_offer_unit',
        'brand_id',
        'category_id',
        'category_sub_id',
        'prod_type_id',
        'supplier_id',
        'prod_status_id',
        'status_aktif',
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
     * Many to One relationship with ProductStatus model.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'prod_status_id');
    }

    /**
     * Many to One relationship with Brand model.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Many to One relationship with ProductType model.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'prod_type_id');
    }    

    /**
     * One to Many relationship with ProductInfoLmt model.
     */
    public function productInfoLmts(): HasMany
    {
        return $this->hasMany(ProductInfoLmt::class, 'prod_number');
    }

    /**
     * One to Many relationship with DataReturDetail model.
     */
    public function dataReturDetails(): HasMany
    {
        return $this->hasMany(DataReturDetail::class, 'itemCode');
    }
}
