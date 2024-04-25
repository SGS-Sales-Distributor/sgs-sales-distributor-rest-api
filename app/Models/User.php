<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $table = "user_info";

    protected $fillable = [
        'id',
        'number',
        'nik',
        'fullname',
        'phone',
        'email',
        'username',
        'password',
        'type_id',
        'status',
        'cabang_id',
        'store_id',
        'status_ba',
        'modtime',
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
            'modtime' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
