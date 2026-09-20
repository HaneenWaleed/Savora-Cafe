<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Favorite extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'favorable_id',
        'favorable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorable()
    {
        return $this->morphTo();
    }
}
