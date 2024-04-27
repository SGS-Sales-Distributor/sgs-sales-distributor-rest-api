<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCustomerSales extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'order_customer_sales';

    protected $fillable = [
        'no_order',
        'tgl_order',
        'tipe',
        'company',
        'top',
        'cust_code',
        'ship_code',
        'whs_code',
        'whs_code_to',
        'order_sts',
        'totOrderQty',
        'totReleaseQty',
        'keterangan',
        'llb_gabungan_reff',
        'llb_gabungan_sts',
        'uploaded_at',
        'uploaded_by',
        'store_id',
        'status_id',
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
            'deleted_at' => 'datetime:Y-m-d H:i:s'
        ];
    }

    /**
     * Many to One relationship with MasterStatus model.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(MasterStatus::class, 'status_id');
    }

    /**
     * Many to One relationship with StoreInfoDistri model.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfoDistri::class, 'store_id');
    }

    /**
     * One to Many relationship with OrderCustomerSalesDetail model.
     */
    public function details(): HasMany
    {
        return $this->hasMany(OrderCustomerSalesDetail::class, 'orderId');
    }
}
