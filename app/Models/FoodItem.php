<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'ingredients',
        'calories',
        'spicy_level',
        'quantity',
        'preparation_time',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ingredients' => 'array',
            'price'       => 'decimal:2',
            'status'      => 'boolean',
        ];
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', true)->where('quantity', '>', 0);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }

    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'purchasable');
    }
}
