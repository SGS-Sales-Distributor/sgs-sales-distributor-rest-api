<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreInfoDistriPerson extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'store_info_distri_person';

    protected $fillable = [
        'store_id',
        'owner',
        'nik_owner',
        'email_owner',
        'ktp_owner',
        'photo_other',
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
     * Many to One relationship with StoreInfoDistri model.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(StoreInfoDistri::class, 'store_id');
    }
}
