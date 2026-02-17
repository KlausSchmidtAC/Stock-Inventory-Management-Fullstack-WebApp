<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes; 
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
        return $this->hasMany(Product::class)
            ->where('count', '>', 0)
            ->where('count', '<', 10);
    }

    /**
     * Get products with count equal to 0.
     */
    public function productsOutOfStock(): HasMany
    {
        return $this->hasMany(Product::class)->where('count', '<=', 0);
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(InventoryTransaction::class, Product::class);
    }

}
