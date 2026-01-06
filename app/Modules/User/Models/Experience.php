<?php

namespace App\Modules\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasUuids;

    protected $fillable = [
        'company',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'description',
        'user_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
