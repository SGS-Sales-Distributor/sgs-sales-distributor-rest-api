<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataRetur extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'data_retur';

    protected $fillable = [
        'custmrCode',
        'custmrName',
        'shiptoCode',
        'termCode',
        'whsCode',
        'whsCodeTo',
        'refference',
        'comments',
        'docDate',
        'proccessDate',
        'transferSts',
        'companyId',
        'trxid',
        'isllb',
        'docentryGR1',
        'docentryGI',
        'docentryGR2',
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
     * One to Many relationship with DataReturDetail model.
     */
    public function details(): HasMany
    {
        return $this->hasMany(DataReturDetail::class, 'baseId');
    }
}
