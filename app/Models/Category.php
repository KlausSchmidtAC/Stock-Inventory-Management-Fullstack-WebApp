<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get products with count less than 10.
     */
    public function productsWithLowStock(): HasMany
    {
        return $this->hasMany(Product::class)->where('count', '<', 10);
    }

    /**
     * Get products with count equal to 0.
     */
    public function productsOutOfStock(): HasMany
    {
        return $this->hasMany(Product::class)->where('count', 0);
    }
}
