<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreInfoDistri extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'store_id';
    
    protected $table = 'store_info_distri';

    protected $fillable = [
        'store_name',
        'store_alias',
        'store_address',
        'store_phone',
        'store_fax',
        'store_type_id',
        'subcabang_id',
        'store_code',
        'active',
        'subcabang_idnew',
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
     * Many to One relationship with StoreType model.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    /**
     * Many to One relationship with StoreCabang model.
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(StoreCabang::class, 'subcabang_id');
    }

    /**
     * Many to One relationship with StoreCabang model using subcabang_idnew field.
     */
    public function cabangNew(): BelongsTo
    {
        return $this->belongsTo(StoreCabang::class, 'subcabang_idnew');
    }

    /**
     * One to Many relationship with ProfilVisit model.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(ProfilVisit::class, 'store_id');
    }

    /**
     * One to Many relationship with StoreInfoDistriPerson model.
     */
    public function owners(): HasMany
    {
        return $this->hasMany(StoreInfoDistriPerson::class, 'store_id');
    }

    /**
     * One to Many relationship with OrderCustomerSales model.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(OrderCustomerSales::class, 'store_id');
    }

    /**
     * One to Many relationship of OrderCustomerDetails through OrderCustomerSales model.
     */
    public function orderDetails(): HasManyThrough
    {
        return $this->hasManyThrough(OrderCustomerSalesDetail::class, OrderCustomerSales::class, 'store_id', 'orderId');
    }

    /**
     * One to Many relationship with MasterCallPlanDetail model.
     */
    public function masterCallPlanDetails(): HasMany
    {
        return $this->hasMany(MasterCallPlanDetail::class, 'store_id');
    }

}
