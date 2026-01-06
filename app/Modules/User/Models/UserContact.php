<?php

namespace App\Modules\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'platform',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
