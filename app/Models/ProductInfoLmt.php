<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductInfoLmt extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    public $incrementing = false;
    
    protected $primaryKey = 'prod_number';
    
    protected $table = 'product_info_lmt';

    protected $fillable = [
        'prod_number',
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
     * Many to One relationship with ProductInfoDo model.
     */
    public function productInfoDo(): BelongsTo
    {
        return $this->belongsTo(ProductInfoDo::class, 'prod_number');
    }

    /**
     * One to Many relationship with OrderCustomerSalesDetail model.
     */
    public function orderCustomerDetails(): HasMany
    {
        return $this->hasMany(OrderCustomerSalesDetail::class, 'itemCodeCust');
    }

    /**
     * One to Many relationship with OrderCustomerSalesDetail model using itemCode field.
     */
    public function orderCustomerDetailsWithItemCode(): HasMany
    {
        return $this->hasMany(OrderCustomerSalesDetail::class, 'itemCode');
    }
}
