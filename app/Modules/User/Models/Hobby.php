<?php

namespace App\Modules\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'hobby_user');
    }
}
