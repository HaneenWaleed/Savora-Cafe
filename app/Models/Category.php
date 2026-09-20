<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'type'];

    public function foodItems()
    {
        return $this->hasMany(FoodItem::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
    }
}