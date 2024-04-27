<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = "program";

    protected $fillable = [
        'id_type_program',
        'name_program',
        'keterangan',
        'active',
        'periode_start',
        'periode_end',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime:Y-m-d H:i:s',
            'periode_start' => 'date:Y-md-d',
            'periode_end' => 'date:Y-md-d',
        ];
    }

    /**
     * Many to One relationship with MasterTypeProgram model.
     */
    public function masterTypeProgram(): BelongsTo
    {
        return $this->belongsTo(MasterTypeProgram::class, 'id_type_program');
    }

    /**
     * One to Many relationship with ProgramDetail model.
     */
    public function details(): HasMany
    {
        return $this->hasMany(ProgramDetail::class, 'id_program');
    }
}
