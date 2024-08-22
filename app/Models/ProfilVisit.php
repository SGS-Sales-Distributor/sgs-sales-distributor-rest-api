<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfilVisit extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'profil_visit';

    protected $fillable = [
        'store_id',
        'user',
        'photo_visit',
        'photo_visit_out',
        'tanggal_visit',
        'time_in',
        'time_out',
        'purchase_order_in',
        'condit_owner',
        'ket',
        'comment_appr',
        'lat_in',
        'long_in',
        'lat_out',
        'long_out',
        'approval',
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
            'tanggal_visit' => 'date:Y-m-d',
            'time_in' => 'datetime:H:i:s',
            'time_out' => 'datetime:H:i:s',
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

    /**
     * Many to One relationship with User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function masterPlanDtl() : BelongsTo {
        return $this->belongsTo(MasterCallPlanDetail::class,'store_id');        
    }
}
