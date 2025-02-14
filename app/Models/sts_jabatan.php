<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class sts_jabatan extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'sts_jabatan';

    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'jabatan',
        'level_atas',
        'level_atas_1',
        'level_atas_2',
        'created_by',
        'created_at'
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

    public function get_data_by_search($search, $arr_pagination)
    {
        $arr_pagination['limit'] = 10;
        if (!empty($search))
            $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = sts_jabatan::whereRaw("( jabatan like '%$search%' OR level_atas like '%$search%') OR level_atas_1 like '%$search%') OR level_atas_2 like '%$search%') AND deleted_at is NULL")
            ->select('id', 'jabatan', 'level_atas', 'level_atas_1', 'level_atas_2')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')->get();
        return $data;
        // OR lower (obj_type) like '%$search%')
    }

    public function get_data()
    {
        $data = sts_jabatan::select('id', 'jabatan', 'level_atas', 'level_atas_1', 'level_atas_2')
            ->orderBy('id', 'ASC')->get();
        if (!$data) {
            return json_encode('Data Kosong');
        }

        return $data;
        // OR lower (obj_type) like '%$search%')
    }

    public function get_data_by_id($id)
    {
        $data = sts_jabatan::select('id', 'jabatan', 'level_atas', 'level_atas_1', 'level_atas_2')
            ->where('id', $id)
            ->orderBy('id', 'ASC')->first();

        if (!$data) {
            return json_encode('Data Kosong');
        }
        return $data;
        // OR lower (obj_type) like '%$search%')
    }
}
