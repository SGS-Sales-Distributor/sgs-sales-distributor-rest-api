<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class profilNotvisit extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;


    protected $table="profil_notvisit";

    protected $fillable = [
        'id',
        'id_master_call_plan_detail',
        'ket',
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
