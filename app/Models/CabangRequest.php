<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabangRequest extends Model
{
    use SoftDeletes;

    protected $table = 'cabang_request';

    protected $fillable = [
        'id',
        'user_id',
        'cabang_id',
        'kode_lokasi',
        'nama_cabang',
        'status',
        'note',
        'approved_by_name',
        'rejected_by_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}