<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterCallPlan extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = "master_call_plan";

    protected $fillable = [
        'month_plan',
        'year_plan',
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * One to Many relationship with MasterCallPlan model.
     */
    public function details(): HasMany
    {
        return $this->hasMany(MasterCallPlanDetail::class, 'call_plan_id');
    }
}
