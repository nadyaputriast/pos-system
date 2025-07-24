<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
    ];
}