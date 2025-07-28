<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;

class Feedback extends Model
{
    protected $table = 'feedback';
    public $incrementing = true;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'client_id',
        'rating',
        'comment',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
