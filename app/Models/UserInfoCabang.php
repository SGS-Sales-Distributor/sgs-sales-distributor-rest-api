<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserInfoCabang extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = true;
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'cabang_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
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
}
