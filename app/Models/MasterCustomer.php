<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MasterCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mst_customer';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'customer_address',
        'customer_pos_code',
        'status',
        'prefix',
        'unit_code',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}