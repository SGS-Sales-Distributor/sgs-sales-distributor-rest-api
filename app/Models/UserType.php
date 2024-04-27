<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'user_type_id';

    protected $table = 'user_type';

    protected $fillable = [
        'user_type_name',
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
     * One to Many relationship with User model.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type_id');
    }
}
