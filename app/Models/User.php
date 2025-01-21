<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Allow mass assignment for these fields
    protected $fillable = [
        'username',
        'fullname',
        'email',
        'password',
        'admin',
        'tool_preferences',
    ];

    // Cast the tool_preferences column to JSON
    protected $casts = [
        'tool_preferences' => 'array',
    ];

    // Relationship with groups
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }
}
