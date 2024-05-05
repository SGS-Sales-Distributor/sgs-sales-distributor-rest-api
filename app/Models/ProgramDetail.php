<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;
    
    protected $table = 'program_detail';

    protected $fillable = [
        'id_program',
        'condition',
        'get',
        'product',
        'qty',
        'disc_val',
        'created_by',
        'updated_by'
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
     * Many to One relationship with Program model.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'id_program');
    }
}
