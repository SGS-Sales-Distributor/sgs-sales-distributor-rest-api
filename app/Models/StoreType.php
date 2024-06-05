<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreType extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'store_type_id';

    protected $table = 'store_type';

    protected $fillable = [
        'store_type_name',
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
     * One to Many relationship with StoreInfoDistri model.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(StoreInfoDistri::class, 'store_type_id');
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;

        $data = StoreType::where('store_type_id', 'like', "%$search%")
            ->orWhere('store_type_name', 'like', "%$search%")
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('created_at', 'desc')->get();
        return $data;
    }

    public function getcboIDStore()
    {
        $data = StoreType::select('store_type_id as code', 'store_type_name as label')
            ->orderBy('store_type_id')
            ->get();
        return $data;
    }
}
