<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletRequest extends Model
{
    use SoftDeletes;

    protected $table = 'outlet_request';

    protected $fillable = [
        'store_name',
        'store_alias',
        'store_address',
        'store_phone',
        'store_type_id',
        'subcabang_id',
        'owner',
        'nik_owner',
        'email_owner',
        'requested_by',
        'requested_by_name',
        'status',
        'notes',
        'approved_by_name',
        'rejected_by_name',
        'customer_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // relasi ke user (MD yang request)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    // relasi ke cabang
    public function cabang()
    {
        return $this->belongsTo(StoreCabang::class, 'subcabang_id', 'id');
    }
}
