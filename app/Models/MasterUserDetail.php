<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterUserDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'master_user_detail';

    protected $fillable = [
        'user',
        'groupcode',
        'entrytime',
        'entryuser',
        'entryip',
        'updatetime',
        'updateuser',
        'updateip',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entrytime' => 'datetime:Y-m-d H:i:s',
            'updatetime' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s'
        ];
    }

    /**
     * Many to One relationship with MasterUser model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MasterUser::class, 'user');
    }
}
