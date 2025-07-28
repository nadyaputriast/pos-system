<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    public $incrementing = true;

    protected $fillable = [
        'total_amount',
        'tax',
        'status',
        'payment_method',
        'bank_name',
        'payment_proof',
        'payment_note',
        'deadline',
        'paid_amount',
        'ppn',
        'pph',
        'client_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_products')
            ->withPivot(['qty', 'subtotal'])
            ->withTimestamps();
    }
}
