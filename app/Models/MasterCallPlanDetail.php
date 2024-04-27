<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCallPlanDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'call_plan_id';

    protected $table = 'master_call_plan_detail';

    protected $fillable = [
        'store_id',
        'date',
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
            'date' => 'date:Y-m-d',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * Many to One relationship with MasterCallPlan model.
     */
    public function masterCallPlan(): BelongsTo
    {
        return $this->belongsTo(MasterCallPlan::class, 'call_plan_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfoDistri::class, 'store_id');
    }
}
