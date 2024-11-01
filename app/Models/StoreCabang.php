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

    protected $primaryKey = "id";

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

    public function get_data_($search, $arr_pagination)
    {
        $arr_pagination['limit'] = 10;
        if (!empty($search)) $arr_pagination['offset'] = 0 ;
        $search = strtolower($search);
        $data = StoreCabang::whereRaw("( lower(kode_cabang) like '%$search%' OR lower (nama_cabang) like '%$search%') AND deleted_at is NULL")
            ->select('id','province_id', 'kode_cabang', 'nama_cabang')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')->get();
        return $data;
        // OR lower (obj_type) like '%$search%')
    }
}
