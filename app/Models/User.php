<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'user_id';

    protected $table = "user_info";

    protected $fillable = [
        'number',
        'nik',
        'fullname',
        'phone',
        'email',
        'name',
        'password',
        'type_id',
        'status',
        'cabang_id',
        'store_id',
        'status_ba',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * Many to One relationship with UserStatus model.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'status');
    }

    /**
     * Many to One relationship with UserType model.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'type_id');
    }

    /**
     * One to Many relationship with ProfilVisit model.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(ProfilVisit::class, 'user');
    }

    

    /**
     * One to Many relationship with MasterCallPlan model.
     */
    public function masterCallPlans(): HasMany
    {
        return $this->hasMany(MasterCallPlan::class, 'user_id');
    }

    public function masterCallPlanDetails(): HasManyThrough
    {
        return $this->hasManyThrough(MasterCallPlanDetail::class, MasterCallPlan::class, 'user_id', 'call_plan_id');
    }
}
