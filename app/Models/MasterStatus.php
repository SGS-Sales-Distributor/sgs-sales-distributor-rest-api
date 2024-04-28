<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterStatus extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'master_status';

    protected $fillable = [
        'status_name',
        'created_by',
        'updated_by'
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
     * One to Many relationship with OrderCustomerSales model.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(OrderCustomerSales::class, 'status_id');
    }

    /**
     * One to Many relationship of OrderCustomerSalesDetail through OrderCustomerSales model.
     */
    public function orderDetails(): HasManyThrough
    {
        return $this->hasManyThrough(OrderCustomerSalesDetail::class, OrderCustomerSales::class, 'status_id', 'orderId');
    }
}
