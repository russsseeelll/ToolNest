<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvantiTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ref',
        'subject',
        'timestamp',
    ];
}
