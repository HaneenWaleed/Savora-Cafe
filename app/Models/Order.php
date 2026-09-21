<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    public function cancel(): void
    {
        DB::transaction(function () {
            foreach ($this->items()->get() as $item) {
                $model = $item->orderable_type === 'food' ? FoodItem::class : Beverage::class;

                $model::withTrashed()
                    ->whereKey($item->orderable_id)
                    ->increment('quantity', $item->quantity);
            }

            $this->update([
                'status'         => 'cancelled',
                'payment_status' => $this->payment_status === 'paid' ? 'refunded' : $this->payment_status,
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function scopeSales($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
