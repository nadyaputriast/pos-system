<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    public $incrementing = true;

    protected $fillable = [
        'client_id',
        'total_amount',
        'tax',
        'ppn',
        'pph',
        'status',
        'deadline',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function lastPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }
}
