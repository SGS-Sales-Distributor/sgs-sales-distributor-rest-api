<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderOTP extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "po_otp";

    protected $fillable = [
        'id_po',
        'nomor_po',
        'random_otp',
    ];
}
