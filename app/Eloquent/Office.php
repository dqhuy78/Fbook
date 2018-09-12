<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'name',
        'address',
        'wsm_workspace_id',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}