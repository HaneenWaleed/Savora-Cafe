<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    // use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'purchasable_id',
        'purchasable_type',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchasable()
{
    return $this->morphTo()->withTrashed();
}
}
