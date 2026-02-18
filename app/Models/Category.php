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

    protected $appends = ['products_count', 'products_with_low_stock_count', 'products_out_of_stock_count'];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the count of products, of products with low stock and out of stock in the category.
     * Important: This is not a relationship method, but an accessor to get the count of products in the category. 
     * Assures the existence of real properties, which dont get lost during Livewire`s serialization and deserialization process when surpassing results to child components.  
     * @return int
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    public function getProductsWithLowStockCountAttribute()
    {
        return $this->productsWithLowStock()->count();
    }

    public function getProductsOutOfStockCountAttribute()
    {
        return $this->productsOutOfStock()->count();
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
