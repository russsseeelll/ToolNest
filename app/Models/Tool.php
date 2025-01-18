<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'url', 'colour', 'image', 'allGroups',
    ];


    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }
}



