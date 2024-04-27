<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MasterUser extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public $timestamps = true;
    
    public $incrementing = false;
    
    protected $primaryKey = 'user';
    
    protected $table = 'master_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user',
        'description',
        'password',
        'username',
        'defaultpassword',
        'nik',
        'departmentId',
        'unitId',
        'entrytime',
        'entryuser',
        'entryip',
        'updatetime',
        'updateuser',
        'updateip',
        'avatar',
        'created_by',
        'updated_by',
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
            'entrytime' => 'datetime:Y-m-d H:i:s',
            'updatetime' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s'
        ];
    }

    /**
     * One to Many relationship with MasterUserDetail model.
     */
    public function details(): HasMany
    {
        return $this->hasMany(MasterUserDetail::class, 'user');
    }
}
