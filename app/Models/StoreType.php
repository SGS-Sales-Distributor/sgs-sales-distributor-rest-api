<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreType extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'store_type_id';
    
    protected $table = 'store_type';

    protected $fillable = [
        'store_type_name',
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

    /**
     * One to Many relationship with StoreInfoDistri model.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(StoreInfoDistri::class, 'store_type_id');
    }
}
