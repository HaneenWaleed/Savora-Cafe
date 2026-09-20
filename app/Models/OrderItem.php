<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class OrderItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_id',
        'orderable_id',
        'orderable_type',
        'quantity',
        'price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderable()
    {
        return $this->morphTo();
    }
}
