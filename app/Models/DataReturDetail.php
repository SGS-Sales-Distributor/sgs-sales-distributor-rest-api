<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataReturDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'data_returdetail';

    protected $fillable = [
        'baseId',
        'lineNo',
        'itemCodeBase',
        'itemCode',
        'quantity',
        'disc2',
        'disc3',
        'batchNo',
        'expireDate',
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
     * Many to One relationship with DataRetur model.
     */
    public function retur(): BelongsTo
    {
        return $this->belongsTo(DataRetur::class, 'baseId');
    }

     /**
     * Many to One relationship with ProductInfoDo model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductInfoDo::class, 'itemCode');
    }
}
