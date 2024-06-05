<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCustomerSalesDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'order_customer_sales_detail';

    protected $fillable = [
        'orderId',
        'lineNo',
        'itemCodeCust',
        'itemCode',
        'qtyOrder',
        'releaseOrder',
        'add_disc_1',
        'add_disc_2',
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
     * Many to One relationship with OrderCustomerSales model.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderCustomerSales::class, 'orderId');
    }

    /**
     * Many to One relationship with ProductInfoLmt model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductInfoDo::class, 'itemCodeCust');
    }

    /**
     * Many to One relationship with ProductInfoLmt model using itemCode.
     */
    public function productWithItemCode(): BelongsTo
    {
        return $this->belongsTo(ProductInfoLmt::class, 'itemCode');
    }
}
