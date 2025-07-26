<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'description',
        'price',
        'ppn',
        'pph',
    ];
}