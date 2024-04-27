<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeLokasi extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'kode_lokasi';

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'kode_lokasi',
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
}
