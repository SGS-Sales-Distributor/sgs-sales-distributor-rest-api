<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTypeProgram extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $primaryKey = 'id_type';

    protected $table = 'master_type_program';

    protected $fillable = [
        'type',
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
     * One to Many relationship with Program model.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'id_type_program');
    }

    /**
     * One to Many relationship of ProgramDetails through Program model.
     */
    public function programDetails(): HasManyThrough
    {
        return $this->hasManyThrough(ProgramDetail::class, Program::class, 'id_type_program', 'id_program');
    }

    public function programa()
    {
        return $this->belongsTo(program::class, 'id_type_program', 'id_type');
    }


    public function get_data_($search, $arr_pagination)
    {
    
        if (!empty($search)) $arr_pagination['offset'] = 0;
        
        $data = MasterTypeProgram::where('id_type', 'like', "%$search%")
            ->orWhere('type', 'like', "%$search%")
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('id_type', 'ASC')->get();
        return $data;
    } 
}
