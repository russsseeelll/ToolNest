<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupname',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class);
    }
}
