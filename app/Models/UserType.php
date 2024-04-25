<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_type';

    protected $fillable = [
        'id',
        'name',
        'modtime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'modtime' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
