<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    // ...
}
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

    // افتراض مني: العميل يلغي الأوردر وهو Pending بس (نعدله لو عندكم قاعدة تانية)
    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
