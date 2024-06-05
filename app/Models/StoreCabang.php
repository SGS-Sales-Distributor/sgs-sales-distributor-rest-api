<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCabang extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'store_cabang';

    protected $fillable = [
        'province_id',
        'kode_cabang',
        'nama_cabang',
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
     * Many to One relationship with MasterProvince model.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(MasterProvince::class, 'province_id');
    }

    /**
     * One to Many relationship with StoreInfoDistri model.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(StoreInfoDistri::class, 'subcabang_id');
    }

    /**
     * One to Many relationship with StoreInfoDistri model.
     */
    public function recentStores(): HasMany
    {
        return $this->hasMany(StoreInfoDistri::class, 'subcabang_idnew');
    }

    public function getCboCabangCallplan($salesman)
    {
        $data = StoreCabang::select('province_id as code', 'nama_cabang as label')
        // ->whereRaw("province_id = (select province_id from User where fullname = '".$salesman."')")
            ->orderBy('province_id')
            ->get();
        return $data;
    }

    public function getCboStoreCabang()
    {
        $data = StoreCabang::select('id as code', 'nama_cabang as label')
            ->orderBy('id')
            ->get();
        return $data;
    }
}
