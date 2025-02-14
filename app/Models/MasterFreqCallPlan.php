<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFreqCallPlan extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'master_freq_call_plan';

    protected $fillable = [
        'frekuensi',
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

    public function get_data()
    {
        $data = sts_jabatan::select('id', 'frekuensi', 'created_by')
            ->orderBy('id', 'ASC')->get();
        if (!$data) {
            return json_encode('Data Kosong');
        }

        return $data;
        // OR lower (obj_type) like '%$search%')
    }
}
